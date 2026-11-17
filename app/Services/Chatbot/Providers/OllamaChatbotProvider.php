<?php

namespace App\Services\Chatbot\Providers;

use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Local / self-hosted AI through an Ollama server (any host reachable from
 * Laravel — not necessarily the same machine). Free to run.
 */
class OllamaChatbotProvider extends AbstractChatbotProvider
{
    public function key(): string
    {
        return 'ollama';
    }

    public function label(): string
    {
        return 'Ollama';
    }

    public function configurationErrors(): array
    {
        $errors = [];
        if (blank($this->config->base_url) || ! filter_var($this->config->base_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Ollama server URL is missing or invalid.';
        }
        if (blank($this->config->model_name)) {
            $errors[] = 'Ollama model name is missing.';
        }

        return $errors;
    }

    public function reply(ChatContext $context): ChatbotResponse
    {
        $this->assertConfigured();

        $options = ['num_predict' => $this->maxTokens()];
        if (($temperature = $this->temperature($context)) !== null) {
            $options['temperature'] = $temperature;
        }

        $response = $this->send(fn (PendingRequest $http) => $http->post($this->url('/api/chat'), [
            'model' => $this->config->model_name,
            'messages' => $this->chatMessages($context),
            'stream' => false,
            'options' => $options,
        ]));

        $text = $response->json('message.content');
        if (! is_string($text)) {
            throw ChatbotProviderException::make('ollama', 'Unexpected response from the Ollama server.');
        }

        return $this->aiResponse($text, $context, [
            'prompt_tokens' => $response->json('prompt_eval_count'),
            'completion_tokens' => $response->json('eval_count'),
        ]);
    }

    public function testConnection(): array
    {
        if ($errors = $this->configurationErrors()) {
            return ['ok' => false, 'message' => implode(' ', $errors)];
        }

        try {
            $response = $this->send(fn (PendingRequest $http) => $http->get($this->url('/api/tags')));
        } catch (ChatbotProviderException $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }

        $installed = collect($response->json('models', []))
            ->flatMap(fn ($m) => [$m['name'] ?? null, $m['model'] ?? null])
            ->filter()->unique()->values();

        $model = $this->config->model_name;
        $found = $installed->contains($model) || $installed->contains($model . ':latest');

        if (! $found) {
            return [
                'ok' => false,
                'message' => "Connected to Ollama, but model \"{$model}\" is not installed. Run: ollama pull {$model}"
                    . ($installed->isNotEmpty() ? '. Installed: ' . $installed->implode(', ') : ''),
            ];
        }

        return ['ok' => true, 'message' => "Connected to Ollama. Model \"{$model}\" is available."];
    }

    private function assertConfigured(): void
    {
        if ($errors = $this->configurationErrors()) {
            throw ChatbotProviderException::make('ollama', implode(' ', $errors));
        }
    }

    private function url(string $path): string
    {
        return rtrim((string) $this->config->base_url, '/') . $path;
    }

    /** @param  callable(PendingRequest): Response  $call */
    private function send(callable $call): Response
    {
        try {
            $response = $call(Http::acceptJson()->timeout($this->timeout())->connectTimeout(min(10, $this->timeout())));
        } catch (ConnectionException $e) {
            $timedOut = str_contains(strtolower($e->getMessage()), 'timed out') || str_contains($e->getMessage(), 'cURL error 28');
            throw ChatbotProviderException::make('ollama', $timedOut
                ? 'The Ollama server did not respond within ' . $this->timeout() . ' seconds.'
                : 'Could not connect to the Ollama server at ' . $this->config->base_url . '.');
        }

        if ($response->failed()) {
            $error = (string) ($response->json('error') ?? '');
            if ($response->status() === 404 && str_contains(strtolower($error), 'model')) {
                throw ChatbotProviderException::make('ollama', "Model \"{$this->config->model_name}\" was not found on the Ollama server.");
            }

            throw ChatbotProviderException::make('ollama', 'Ollama returned HTTP ' . $response->status()
                . ($error !== '' ? ': ' . mb_substr($error, 0, 200) : '.'));
        }

        return $response;
    }
}
