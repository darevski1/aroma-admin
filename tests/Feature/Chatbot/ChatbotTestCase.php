<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotKnowledge;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Models\Chatbot\ChatbotSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

abstract class ChatbotTestCase extends TestCase
{
    use RefreshDatabase;

    protected const FAKE_KEY = 'sk-test-SECRET-key-1234567890';

    protected function setUp(): void
    {
        parent::setUp();
        // No real network calls (and never a paid API request) from the test suite.
        Http::preventStrayRequests();
        RateLimiter::clear('chatbot:openai:hour');
        RateLimiter::clear('chatbot:openai:day');
    }

    protected function admin(): User
    {
        $user = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $user->email_verified_at = now();

        return $user;
    }

    protected function settings(array $changes = []): ChatbotSetting
    {
        $settings = ChatbotSetting::current();
        if ($changes) {
            $settings->update($changes);
        }

        return $settings;
    }

    protected function faq(string $question, string $answer, array $keywords = [], string $language = 'mk', bool $active = true): ChatbotFaq
    {
        return ChatbotFaq::create(compact('question', 'answer', 'keywords', 'language') + ['is_active' => $active]);
    }

    protected function knowledge(string $title, string $content, string $language = 'en'): ChatbotKnowledge
    {
        return ChatbotKnowledge::create(compact('title', 'content', 'language') + ['type' => 'text', 'is_active' => true]);
    }

    protected function enableOllama(): ChatbotProviderSetting
    {
        $config = ChatbotProviderSetting::for('ollama');
        $config->update([
            'is_enabled' => true,
            'base_url' => 'http://ollama.test:11434',
            'model_name' => 'llama3.2',
            'additional_settings' => ['timeout' => 5, 'temperature' => 0.2, 'max_tokens' => 300],
        ]);
        $this->settings(['provider' => 'ollama']);

        return $config;
    }

    protected function enableOpenAi(array $extra = []): ChatbotProviderSetting
    {
        config(['chatbot.openai.api_key' => self::FAKE_KEY]);
        $config = ChatbotProviderSetting::for('openai');
        $config->update([
            'is_enabled' => true,
            'api_key_reference' => 'env',
            'model_name' => 'test-model',
            'base_url' => 'https://api.openai.test/v1',
            'additional_settings' => $extra + [
                'timeout' => 5, 'temperature' => 0.2, 'max_tokens' => 300,
                'max_requests_per_hour' => 60, 'max_requests_per_day' => 300,
            ],
        ]);
        $this->settings(['provider' => 'openai']);

        return $config;
    }
}
