<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Website chatbot: settings, provider settings, FAQ, knowledge base and
 * conversation history. Seeds the initial configuration: chatbot enabled,
 * FAQ Engine active, Ollama and OpenAI disabled (no paid service is active).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->boolean('show_on_frontend')->default(true);
            $table->string('bot_name', 100);
            $table->text('welcome_message')->nullable();
            $table->longText('system_instructions')->nullable();
            $table->json('instruction_sections')->nullable();
            $table->string('default_language', 5)->default('mk');
            $table->text('fallback_message')->nullable();
            $table->text('unavailable_message')->nullable();
            $table->string('provider', 20)->default('faq');
            $table->boolean('fallback_to_faq')->default(true);
            $table->unsignedInteger('response_limit')->default(1200);
            $table->decimal('temperature', 3, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_provider_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 20)->unique();
            $table->string('model_name')->nullable();
            $table->string('base_url')->nullable();
            $table->string('api_key_reference')->nullable();
            $table->text('api_key_encrypted')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('additional_settings')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('chatbot_categories')->nullOnDelete();
            $table->text('question');
            $table->longText('answer');
            $table->json('keywords')->nullable();
            $table->string('language', 5)->default('mk');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            // Normalized (lowercase, transliterated) text used for matching.
            $table->longText('search_text')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'language']);
            $table->index('sort_order');
        });

        Schema::create('chatbot_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->string('type', 20)->default('text');
            $table->string('source')->nullable();
            $table->string('language', 5)->default('mk');
            $table->foreignId('category_id')->nullable()->constrained('chatbot_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->longText('search_text')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'language']);
        });

        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            // Random token kept in the visitor's session; owner check for history.
            $table->string('session_id', 64)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('started_at');
            $table->index('last_activity_at');
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->string('sender', 10); // user | bot
            $table->longText('message');
            $table->string('provider', 20)->nullable();
            // faq | ai | fallback | error (bot replies; copied onto the visitor question)
            $table->string('response_type', 20)->nullable();
            $table->boolean('answered')->nullable();
            $table->foreignId('matched_faq_id')->nullable()->constrained('chatbot_faqs')->nullOnDelete();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->decimal('estimated_cost', 12, 6)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender', 'created_at']);
            $table->index(['provider', 'created_at']);
            $table->index(['sender', 'answered']);
        });

        $now = now();

        DB::table('chatbot_settings')->insert([
            'enabled' => true,
            'show_on_frontend' => true,
            'bot_name' => 'ITPlus Асистент',
            'welcome_message' => "Здраво! 👋 Јас сум виртуелниот асистент на ITPlus. Како можам да ви помогнам?\nHi! How can I help you today?",
            'default_language' => 'mk',
            'fallback_message' => "За жал, немам доволно информации за ова прашање. Ве молиме контактирајте не на info@itplus.mk или +389 71 354 822.\nSorry, I don't have enough information about that. Please contact us at info@itplus.mk or +389 71 354 822.",
            'unavailable_message' => "Асистентот моментално не е достапен. Обидете се повторно подоцна или контактирајте не на info@itplus.mk.\nThe assistant is temporarily unavailable. Please try again later or contact info@itplus.mk.",
            'provider' => 'faq',
            'fallback_to_faq' => true,
            'response_limit' => 1200,
            'temperature' => 0.3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('chatbot_provider_settings')->insert([
            [
                'provider' => 'faq',
                'model_name' => null,
                'base_url' => null,
                'api_key_reference' => null,
                'is_enabled' => true,
                'additional_settings' => json_encode(['threshold' => 0.5, 'language_matching' => true, 'keyword_matching' => true]),
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'provider' => 'ollama',
                'model_name' => config('chatbot.ollama.model', 'llama3.2'),
                'base_url' => config('chatbot.ollama.base_url', 'http://127.0.0.1:11434'),
                'api_key_reference' => null,
                'is_enabled' => false,
                'additional_settings' => json_encode(['timeout' => 30, 'temperature' => 0.3, 'max_tokens' => 500]),
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'provider' => 'openai',
                'model_name' => config('chatbot.openai.model', 'gpt-4o-mini'),
                'base_url' => config('chatbot.openai.base_url', 'https://api.openai.com/v1'),
                'api_key_reference' => 'env',
                'is_enabled' => false,
                'additional_settings' => json_encode([
                    'timeout' => 30, 'temperature' => 0.3, 'max_tokens' => 500,
                    'max_requests_per_hour' => 60, 'max_requests_per_day' => 300,
                    'input_price_per_million' => null, 'output_price_per_million' => null,
                ]),
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('chatbot_knowledge');
        Schema::dropIfExists('chatbot_faqs');
        Schema::dropIfExists('chatbot_categories');
        Schema::dropIfExists('chatbot_provider_settings');
        Schema::dropIfExists('chatbot_settings');
    }
};
