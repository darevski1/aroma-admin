<?php

namespace Tests\Feature\Chatbot;

use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\InstructionService;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class OllamaProviderTest extends ChatbotTestCase
{
    public function test_successful_reply_uses_system_instructions_and_relevant_knowledge_only(): void
    {
        $this->enableOllama();
        app(InstructionService::class)->save($this->settings(), ['role' => 'You are the UNIQUE-ROLE-MARKER assistant.']);
        $this->knowledge('Warranty policy', 'All printers have a 24 month warranty.');
        $this->knowledge('Office plants', 'We water the ficus on Mondays.');

        Http::fake(['ollama.test:11434/api/chat' => Http::response([
            'message' => ['role' => 'assistant', 'content' => 'Printers have a 24 month warranty.'],
            'prompt_eval_count' => 120, 'eval_count' => 12,
        ])]);

        $r = app(ChatbotManager::class)->respond('What is the warranty on printers?');

        $this->assertSame(ChatbotResponse::TYPE_AI, $r->type);
        $this->assertSame('ollama', $r->provider);
        $this->assertTrue($r->answered);
        $this->assertSame('Printers have a 24 month warranty.', $r->message);
        $this->assertSame(120, $r->promptTokens);

        Http::assertSent(function (Request $request) {
            $system = $request['messages'][0]['content'];

            return $request->url() === 'http://ollama.test:11434/api/chat'
                && $request['model'] === 'llama3.2'
                && $request['stream'] === false
                && $request['options']['temperature'] == 0.2
                && $request['options']['num_predict'] === 300
                && $request['messages'][0]['role'] === 'system'
                && str_contains($system, 'UNIQUE-ROLE-MARKER')
                && str_contains($system, '24 month warranty')
                && ! str_contains($system, 'ficus')
                && end($request->data()['messages'])['content'] === 'What is the warranty on printers?';
        });
    }

    public function test_no_answer_marker_marks_the_question_unanswered(): void
    {
        $this->enableOllama();
        Http::fake(['*/api/chat' => Http::response(['message' => ['content' => InstructionService::NO_ANSWER_MARKER . ' I do not have that information.']])]);

        $r = app(ChatbotManager::class)->respond('What is the CEO\'s birthday?');

        $this->assertFalse($r->answered);
        $this->assertSame('I do not have that information.', $r->message);
    }

    public function test_connection_failure_falls_back_to_free_faq_engine(): void
    {
        $this->enableOllama();
        $this->faq('How can I contact you?', 'Call us.', [], 'en');
        Http::fake(fn () => throw new ConnectionException('cURL error 7: Failed to connect'));

        $r = app(ChatbotManager::class)->respond('How can I contact you?');

        $this->assertSame('faq', $r->provider);
        $this->assertSame('Call us.', $r->message);
        $this->assertSame('ollama', $r->metadata['fallback_from']);
        $this->assertStringContainsString('Could not connect', $r->metadata['error']);
    }

    public function test_connection_failure_without_fallback_shows_unavailable_message(): void
    {
        $this->enableOllama();
        $this->settings(['fallback_to_faq' => false]);
        Http::fake(fn () => throw new ConnectionException('cURL error 7: Failed to connect'));

        $r = app(ChatbotManager::class)->respond('Hello?');

        $this->assertSame(ChatbotResponse::TYPE_ERROR, $r->type);
        $this->assertSame($this->settings()->unavailable_message, $r->message);
    }

    public function test_timeout_is_reported(): void
    {
        $this->enableOllama();
        Http::fake(fn () => throw new ConnectionException('cURL error 28: Operation timed out after 5001 milliseconds'));

        $this->expectException(ChatbotProviderException::class);
        $this->expectExceptionMessage('did not respond within 5 seconds');

        app(ChatbotManager::class)->provider('ollama')->reply(new ChatContext('hi', 'en', $this->settings()));
    }

    public function test_invalid_model_is_reported(): void
    {
        $this->enableOllama();
        Http::fake(['*/api/chat' => Http::response(['error' => "model 'llama3.2' not found"], 404)]);

        $this->expectException(ChatbotProviderException::class);
        $this->expectExceptionMessage('Model "llama3.2" was not found');

        app(ChatbotManager::class)->provider('ollama')->reply(new ChatContext('hi', 'en', $this->settings()));
    }

    public function test_connection_test_checks_the_model_is_installed(): void
    {
        $this->enableOllama();
        Http::fakeSequence('*/api/tags')
            ->push(['models' => [['name' => 'llama3.2:latest', 'model' => 'llama3.2:latest']]])
            ->push(['models' => [['name' => 'mistral:latest']]]);

        $provider = app(ChatbotManager::class)->provider('ollama');

        $this->assertTrue($provider->testConnection()['ok']);
        $failed = $provider->testConnection();
        $this->assertFalse($failed['ok']);
        $this->assertStringContainsString('ollama pull llama3.2', $failed['message']);
    }

    public function test_connection_test_reports_unreachable_server(): void
    {
        $this->enableOllama();
        Http::fake(fn () => throw new ConnectionException('cURL error 7'));

        $result = app(ChatbotManager::class)->provider('ollama')->testConnection();

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Could not connect to the Ollama server', $result['message']);
    }

    public function test_disabled_ollama_is_never_called(): void
    {
        $this->enableOllama()->update(['is_enabled' => false]);
        Http::fake();

        $r = app(ChatbotManager::class)->respond('Hello');

        Http::assertNothingSent();
        $this->assertSame('faq', $r->provider);
        $this->assertSame('ollama', $r->metadata['fallback_from']);
    }
}
