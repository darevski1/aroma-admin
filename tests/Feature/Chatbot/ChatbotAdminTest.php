<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotCategory;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotKnowledge;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ChatbotAdminTest extends ChatbotTestCase
{
    private const PAGES = [
        '/admin/chatbot', '/admin/chatbot/settings', '/admin/chatbot/providers', '/admin/chatbot/instructions',
        '/admin/chatbot/knowledge', '/admin/chatbot/knowledge/create', '/admin/chatbot/faqs', '/admin/chatbot/faqs/create',
        '/admin/chatbot/categories', '/admin/chatbot/categories/create', '/admin/chatbot/conversations', '/admin/chatbot/statistics',
    ];

    public function test_guests_and_non_admins_cannot_access_admin(): void
    {
        foreach (self::PAGES as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/admin/chatbot/providers/openai/test')->assertRedirect('/login');

        $user = new User(['name' => 'Editor', 'email' => 'u@example.test', 'user_role' => 1]);
        $user->email_verified_at = now();
        $this->actingAs($user);
        foreach (self::PAGES as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->put('/admin/chatbot/providers/active', ['provider' => 'faq'])->assertForbidden();
    }

    public function test_all_admin_pages_render_with_data(): void
    {
        $this->faq('Question?', 'Answer.');
        $this->knowledge('Title', 'Content');
        $this->postJson('/chatbot/message', ['message' => 'Question?']);
        $this->postJson('/chatbot/message', ['message' => 'Something unknown']);

        $this->actingAs($this->admin());
        foreach (self::PAGES as $url) {
            $this->get($url)->assertOk()->assertSee('AI Provider Settings');
        }
        $this->get('/admin/chatbot/conversations/' . ChatbotConversation::sole()->id)
            ->assertOk()->assertSee('Something unknown')->assertSee('not answered');
        $this->get('/admin/chatbot/conversations?unanswered=1&search=unknown&from=2020-01-01')->assertOk()->assertSee('Something unknown');
        $this->get('/admin/chatbot')->assertSee('Something unknown');
    }

    public function test_general_settings_update(): void
    {
        $this->actingAs($this->admin())->put('/admin/chatbot/settings', [
            'bot_name' => 'Aroma Bot', 'welcome_message' => 'Welcome!', 'default_language' => 'en',
            'fallback_message' => 'No idea.', 'unavailable_message' => 'Down.', 'response_limit' => 500,
            'enabled' => '1',
        ])->assertRedirect('/admin/chatbot/settings');

        $s = $this->settings()->fresh();
        $this->assertSame('Aroma Bot', $s->bot_name);
        $this->assertTrue($s->enabled);
        $this->assertFalse($s->show_on_frontend);
        $this->assertSame(500, $s->response_limit);
    }

    public function test_faq_crud_toggle_and_bulk(): void
    {
        $this->actingAs($this->admin());
        $category = ChatbotCategory::create(['name' => 'Општо']);
        $this->assertSame('opshto', $category->slug);

        $this->post('/admin/chatbot/faqs', [
            'question' => 'Do you deliver?', 'answer' => 'Yes.', 'keywords' => 'delivery, shipping ,  ',
            'language' => 'en', 'category_id' => $category->id, 'is_active' => '1',
        ])->assertRedirect('/admin/chatbot/faqs');

        $faq = ChatbotFaq::sole();
        $this->assertSame(['delivery', 'shipping'], $faq->keywords);
        $this->assertStringContainsString('delivery', $faq->search_text);

        $this->put("/admin/chatbot/faqs/{$faq->id}", ['question' => 'Do you ship?', 'answer' => 'Yes!', 'language' => 'en'])->assertRedirect();
        $this->assertFalse($faq->fresh()->is_active);

        $this->patchJson("/admin/chatbot/faqs/{$faq->id}/toggle")->assertJson(['is_active' => true]);

        $other = $this->faq('Other', 'Other answer');
        $this->post('/admin/chatbot/faqs/bulk', ['action' => 'deactivate', 'ids' => [$faq->id, $other->id]])->assertRedirect();
        $this->assertSame(0, ChatbotFaq::where('is_active', true)->count());

        $this->get('/admin/chatbot/faqs?search=ship&language=en&category=' . $category->id)->assertSee('Do you ship?')->assertDontSee('Other answer');

        $this->post('/admin/chatbot/faqs/bulk', ['action' => 'delete', 'ids' => [$other->id]]);
        $this->delete("/admin/chatbot/faqs/{$faq->id}")->assertRedirect();
        $this->assertSame(0, ChatbotFaq::count());
    }

    public function test_knowledge_and_category_crud(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/chatbot/categories', ['name' => 'Pricing', 'is_active' => '1'])->assertRedirect();
        $category = ChatbotCategory::sole();

        $this->post('/admin/chatbot/knowledge', [
            'title' => 'Price list', 'content' => 'Printer service starts at 1000 MKD.', 'type' => 'text',
            'language' => 'en', 'category_id' => $category->id, 'is_active' => '1',
        ])->assertRedirect('/admin/chatbot/knowledge');
        $entry = ChatbotKnowledge::sole();

        $this->get('/admin/chatbot/knowledge?search=Printer')->assertSee('Price list');
        $this->patchJson("/admin/chatbot/knowledge/{$entry->id}/toggle")->assertJson(['is_active' => false]);
        $this->put("/admin/chatbot/knowledge/{$entry->id}", ['title' => 'Prices', 'content' => 'x', 'type' => 'url', 'language' => 'mk'])->assertRedirect();
        $this->assertSame('Prices', $entry->fresh()->title);

        $this->delete("/admin/chatbot/categories/{$category->id}")->assertRedirect();
        $this->assertNull($entry->fresh()->category_id, 'entries are kept when their category is deleted');

        $this->delete("/admin/chatbot/knowledge/{$entry->id}")->assertRedirect();
        $this->assertSame(0, ChatbotKnowledge::count());
    }

    public function test_provider_switching_rules(): void
    {
        $this->actingAs($this->admin());

        // Cannot activate a disabled provider.
        $this->from('/admin/chatbot/providers')->put('/admin/chatbot/providers/active', ['provider' => 'ollama'])
            ->assertRedirect('/admin/chatbot/providers')->assertSessionHas('error');
        $this->assertSame('faq', $this->settings()->fresh()->provider);

        // Enable + configure Ollama, then switch.
        $this->put('/admin/chatbot/providers/ollama', [
            'is_enabled' => '1', 'base_url' => 'http://10.0.0.5:11434/', 'model_name' => 'llama3.2',
            'timeout' => 20, 'temperature' => '', 'max_tokens' => 400,
        ])->assertSessionHas('success');
        $ollama = ChatbotProviderSetting::for('ollama');
        $this->assertSame('http://10.0.0.5:11434', $ollama->base_url);
        $this->assertArrayNotHasKey('temperature', $ollama->additional_settings);

        $this->put('/admin/chatbot/providers/active', ['provider' => 'ollama'])->assertSessionHas('success');
        $this->assertSame('ollama', $this->settings()->fresh()->provider);

        // The active provider cannot be disabled.
        $this->put('/admin/chatbot/providers/ollama', [
            'base_url' => 'http://10.0.0.5:11434', 'model_name' => 'llama3.2', 'timeout' => 20, 'max_tokens' => 400,
        ])->assertSessionHas('error');
        $this->assertTrue(ChatbotProviderSetting::for('ollama')->is_enabled);

        // Conversation history survives switching back.
        $conversation = ChatbotConversation::create(['session_id' => 'x', 'started_at' => now()]);
        $this->put('/admin/chatbot/providers/active', ['provider' => 'faq'])->assertSessionHas('success');
        $this->assertModelExists($conversation);
    }

    public function test_openai_needs_key_and_explicit_paid_confirmation(): void
    {
        $this->actingAs($this->admin());
        config(['chatbot.openai.api_key' => null]);

        $form = [
            'is_enabled' => '1', 'api_key_source' => 'database', 'model_name' => 'gpt-test', 'base_url' => '',
            'timeout' => 30, 'temperature' => '0.3', 'max_tokens' => 300, 'max_requests_per_hour' => 10,
            'max_requests_per_day' => 50, 'input_price_per_million' => '', 'output_price_per_million' => '',
        ];
        $this->put('/admin/chatbot/providers/openai', $form)->assertSessionHas('success');

        $this->put('/admin/chatbot/providers/active', ['provider' => 'openai', 'confirm_paid' => '1'])
            ->assertSessionHas('error', fn ($e) => str_contains($e, 'API key is missing'));

        $this->put('/admin/chatbot/providers/openai', $form + ['api_key' => self::FAKE_KEY])->assertSessionHas('success');

        $this->put('/admin/chatbot/providers/active', ['provider' => 'openai'])
            ->assertSessionHas('error', fn ($e) => str_contains($e, 'billed'));
        $this->assertSame('faq', $this->settings()->fresh()->provider);

        $this->put('/admin/chatbot/providers/active', ['provider' => 'openai', 'confirm_paid' => '1'])->assertSessionHas('success');
        $this->assertSame('openai', $this->settings()->fresh()->provider);
    }

    public function test_api_key_is_never_displayed_or_returned(): void
    {
        $this->actingAs($this->admin());
        ChatbotProviderSetting::for('openai')->update(['api_key_reference' => 'database', 'api_key_encrypted' => self::FAKE_KEY, 'model_name' => 'm']);
        config(['chatbot.openai.api_key' => 'sk-env-OTHER-secret']);

        $html = $this->get('/admin/chatbot/providers')->assertOk()->getContent();
        $this->assertStringNotContainsString(self::FAKE_KEY, $html);
        $this->assertStringNotContainsString('sk-env-OTHER-secret', $html);
        $this->assertStringContainsString('stored key: present', $html);

        $this->assertArrayNotHasKey('api_key_encrypted', ChatbotProviderSetting::for('openai')->toArray());

        // Saving without a new key keeps the stored one.
        $this->put('/admin/chatbot/providers/openai', [
            'api_key_source' => 'database', 'model_name' => 'm', 'timeout' => 30, 'max_tokens' => 300,
            'max_requests_per_hour' => 10, 'max_requests_per_day' => 50,
        ]);
        $this->assertSame(self::FAKE_KEY, ChatbotProviderSetting::for('openai')->api_key_encrypted);
    }

    public function test_connection_test_endpoint(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/admin/chatbot/providers/faq/test')->assertOk()->assertJson(['ok' => true]);

        $this->enableOllama();
        Http::fake(['*/api/tags' => Http::response(['models' => [['name' => 'llama3.2:latest']]])]);
        $this->postJson('/admin/chatbot/providers/ollama/test')->assertOk()->assertJson(['ok' => true]);

        config(['chatbot.openai.api_key' => null]);
        $this->postJson('/admin/chatbot/providers/openai/test')->assertStatus(422)->assertJson(['ok' => false]);
        $this->postJson('/admin/chatbot/providers/unknown/test')->assertNotFound();
    }

    public function test_instructions_are_saved_and_can_be_reset(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/chatbot/instructions', ['sections' => ['role' => 'You are Aroma Bot.', 'tone' => 'Friendly.'], 'temperature' => '0.5'])
            ->assertRedirect('/admin/chatbot/instructions');
        $s = $this->settings()->fresh();
        $this->assertStringContainsString('You are Aroma Bot.', $s->system_instructions);
        $this->assertSame(0.5, $s->temperature);
        $this->get('/admin/chatbot/instructions')->assertSee('You are Aroma Bot.');

        $this->post('/admin/chatbot/instructions/reset')->assertRedirect();
        $this->assertStringContainsString('Never invent prices', $this->settings()->fresh()->system_instructions);
    }

    public function test_conversation_can_be_deleted(): void
    {
        $this->postJson('/chatbot/message', ['message' => 'hello']);
        $conversation = ChatbotConversation::sole();

        $this->actingAs($this->admin())->delete("/admin/chatbot/conversations/{$conversation->id}")->assertRedirect();
        $this->assertModelMissing($conversation);
        $this->assertSame(0, \App\Models\Chatbot\ChatbotMessage::count());
    }
}
