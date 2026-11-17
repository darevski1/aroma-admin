<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\InstructionService;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProviderTest extends ChatbotTestCase
{
    private function completion(string $text = 'Hello from OpenAI.', int $in = 200, int $out = 20): array
    {
        return [
            'choices' => [['message' => ['role' => 'assistant', 'content' => $text]]],
            'usage' => ['prompt_tokens' => $in, 'completion_tokens' => $out, 'total_tokens' => $in + $out],
        ];
    }

    public function test_openai_is_disabled_and_inactive_by_default(): void
    {
        Http::fake();
        $this->faq('Hello there', 'Hi!', [], 'en');

        app(ChatbotManager::class)->respond('Hello there');

        Http::assertNothingSent();
        $this->assertFalse(ChatbotProviderSetting::for('openai')->is_enabled);
    }

    public function test_missing_api_key_is_a_configuration_error_and_sends_nothing(): void
    {
        $this->enableOpenAi();
        config(['chatbot.openai.api_key' => null]);
        Http::fake();

        $provider = app(ChatbotManager::class)->provider('openai');

        $this->assertContains('OpenAI API key is missing (set OPENAI_API_KEY in .env).', $provider->configurationErrors());
        $this->assertFalse($provider->testConnection()['ok']);

        $r = app(ChatbotManager::class)->respond('Hello');
        $this->assertSame('faq', $r->provider);
        Http::assertNothingSent();
    }

    public function test_missing_model_is_a_configuration_error(): void
    {
        $this->enableOpenAi()->update(['model_name' => null]);

        $this->assertContains('OpenAI model is not configured.', app(ChatbotManager::class)->provider('openai')->configurationErrors());
    }

    public function test_reply_sends_instructions_context_and_key_and_tracks_tokens_and_cost(): void
    {
        $this->enableOpenAi(['input_price_per_million' => 1.0, 'output_price_per_million' => 4.0, 'max_tokens' => 300]);
        app(InstructionService::class)->save($this->settings(), ['tone' => 'Be TONE-MARKER friendly.']);
        $this->knowledge('Delivery', 'Delivery in Skopje takes 2 working days.');
        Http::fake(['api.openai.test/v1/chat/completions' => Http::response($this->completion('Delivery takes 2 days.', 1000, 500))]);

        $response = $this->postJson('/chatbot/message', ['message' => 'How long does delivery take?'])->assertOk();

        $response->assertJson(['success' => true, 'message' => 'Delivery takes 2 days.']);
        Http::assertSent(function (Request $request) {
            $system = $request['messages'][0]['content'];

            return $request->hasHeader('Authorization', 'Bearer ' . self::FAKE_KEY)
                && $request['model'] === 'test-model'
                && $request['max_completion_tokens'] === 300
                && str_contains($system, 'TONE-MARKER')
                && str_contains($system, 'Delivery in Skopje takes 2 working days.');
        });

        $bot = ChatbotMessage::where('sender', 'bot')->sole();
        $this->assertSame('openai', $bot->provider);
        $this->assertSame(1000, $bot->prompt_tokens);
        $this->assertSame(500, $bot->completion_tokens);
        $this->assertEqualsWithDelta(0.003, $bot->estimated_cost, 0.0000001); // 1000/1M*1 + 500/1M*4
    }

    public function test_cost_is_not_estimated_without_pricing(): void
    {
        $this->enableOpenAi();
        Http::fake(['*' => Http::response($this->completion())]);

        $r = app(ChatbotManager::class)->respond('Hi');

        $this->assertSame(200, $r->promptTokens);
        $this->assertNull($r->estimatedCost);
    }

    public function test_api_errors_never_leak_the_key(): void
    {
        $this->enableOpenAi();
        Log::spy();
        Http::fake(['*' => Http::response(['error' => ['message' => 'Incorrect API key provided: ' . self::FAKE_KEY]], 401)]);

        try {
            app(ChatbotManager::class)->provider('openai')->reply(new ChatContext('hi', 'en', $this->settings()));
            $this->fail('Expected exception');
        } catch (ChatbotProviderException $e) {
            $this->assertStringContainsString('rejected the API key', $e->getMessage());
            $this->assertStringNotContainsString(self::FAKE_KEY, $e->getMessage());
        }

        $json = $this->postJson('/chatbot/message', ['message' => 'hi'])->getContent();
        $this->assertStringNotContainsString(self::FAKE_KEY, $json);
        $this->assertStringNotContainsString(self::FAKE_KEY, (string) ChatbotMessage::all()->toJson());
        Log::shouldHaveReceived('warning')->withArgs(fn ($m) => ! str_contains($m, self::FAKE_KEY));
    }

    public function test_server_error_is_reported(): void
    {
        $this->enableOpenAi();
        Http::fake(['*' => Http::response(['error' => ['message' => 'The server had an error']], 500)]);

        $this->expectException(ChatbotProviderException::class);
        $this->expectExceptionMessage('OpenAI API error (HTTP 500): The server had an error');

        app(ChatbotManager::class)->provider('openai')->reply(new ChatContext('hi', 'en', $this->settings()));
    }

    public function test_connection_error_is_reported(): void
    {
        $this->enableOpenAi();
        Http::fake(fn () => throw new ConnectionException('cURL error 6: Could not resolve host'));

        $result = app(ChatbotManager::class)->provider('openai')->testConnection();

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Could not connect to the OpenAI API', $result['message']);
    }

    public function test_connection_test_only_reads_model_metadata(): void
    {
        $this->enableOpenAi();
        Http::fake(['api.openai.test/v1/models/test-model' => Http::response(['id' => 'test-model', 'object' => 'model'])]);

        $this->assertTrue(app(ChatbotManager::class)->provider('openai')->testConnection()['ok']);
        Http::assertSent(fn (Request $r) => $r->method() === 'GET' && str_ends_with($r->url(), '/models/test-model'));
        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'chat/completions'));
    }

    public function test_stored_encrypted_key_is_used_when_selected(): void
    {
        $config = $this->enableOpenAi();
        config(['chatbot.openai.api_key' => null]);
        $config->update(['api_key_reference' => 'database', 'api_key_encrypted' => 'sk-db-stored-key']);
        Http::fake(['*' => Http::response($this->completion())]);

        app(ChatbotManager::class)->respond('Hi');

        Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Bearer sk-db-stored-key'));
        $raw = \DB::table('chatbot_provider_settings')->where('provider', 'openai')->value('api_key_encrypted');
        $this->assertNotSame('sk-db-stored-key', $raw);
    }

    public function test_hourly_request_limit_throttles_openai(): void
    {
        $this->enableOpenAi(['max_requests_per_hour' => 1, 'max_requests_per_day' => 10]);
        Http::fake(['*' => Http::response($this->completion())]);

        $first = app(ChatbotManager::class)->respond('Hi');
        $second = app(ChatbotManager::class)->respond('Hi again');

        $this->assertSame('openai', $first->provider);
        $this->assertSame('faq', $second->provider);
        $this->assertStringContainsString('Request limit reached', $second->metadata['error']);
        Http::assertSentCount(1);
    }

    public function test_free_provider_failure_never_falls_back_to_paid_openai(): void
    {
        $this->enableOpenAi();     // configured and enabled …
        $this->enableOllama();     // … but Ollama is the active provider
        Http::fake([
            'ollama.test*' => fn () => throw new ConnectionException('cURL error 7'),
            'api.openai.test*' => Http::response($this->completion()),
        ]);

        $r = app(ChatbotManager::class)->respond('Hello');

        $this->assertSame('faq', $r->provider);
        Http::assertNotSent(fn (Request $req) => str_contains($req->url(), 'openai'));
    }

    public function test_no_answer_marker_is_respected(): void
    {
        $this->enableOpenAi();
        Http::fake(['*' => Http::response($this->completion(InstructionService::NO_ANSWER_MARKER . 'Sorry, no info.'))]);

        $r = app(ChatbotManager::class)->respond('Unknown?');

        $this->assertSame(ChatbotResponse::TYPE_AI, $r->type);
        $this->assertFalse($r->answered);
        $this->assertSame('Sorry, no info.', $r->message);
    }
}
