<?php

namespace App\Services\Chatbot\Providers;

use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Cloud AI through the official OpenAI API (Chat Completions). Paid: it is
 * only used when an administrator enables it and selects it as the active
 * provider, it is never used as a fallback, and requests are capped per hour
 * and per day. The API key comes from .env (OPENAI_API_KEY) or is stored
 * encrypted by an admin; it is never returned to the browser or logged.
 */
class OpenAIChatbotProvider extends AbstractChatbotProvider
{
    public const KEY_SOURCE_ENV = 'env';
    public const KEY_SOURCE_DATABASE = 'database';

    public function key(): string
    {
        return 'openai';
    }

    public function label(): string
    {
        return 'OpenAI';
    }

    public function isPaid(): bool
    {
        return true;
    }

    public function keySource(): string
    {
        return $this->config->api_key_reference === self::KEY_SOURCE_DATABASE ? self::KEY_SOURCE_DATABASE : self::KEY_SOURCE_ENV;
    }

    public function hasApiKey(): bool
    {
        return filled($this->apiKey());
    }

    public function configurationErrors(): array
    {
        $errors = [];
        if (! $this->hasApiKey()) {
            $errors[] = $this->keySource() === self::KEY_SOURCE_ENV
                ? 'OpenAI API key is missing (set OPENAI_API_KEY in .env).'
                : 'OpenAI API key is missing (enter it in AI Provider Settings).';
        }
        if (blank($this->config->model_name)) {
            $errors[] = 'OpenAI model is not configured.';
        }
        if (filled($this->config->base_url) && ! filter_var($this->config->base_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'OpenAI API base URL is invalid.';
        }

        return $errors;
    }

    public function reply(ChatContext $context): ChatbotResponse
    {
        if ($errors = $this->configurationErrors()) {
            throw ChatbotProviderException::make('openai', implode(' ', $errors));
        }

        $this->throttle();

        $payload = [
            'model' => $this->config->model_name,
            'messages' => $this->chatMessages($context),
            'max_completion_tokens' => $this->maxTokens(),
        ];
        if (($temperature = $this->temperature($context)) !== null) {
            $payload['temperature'] = $temperature;
        }

        $response = $this->send(fn (PendingRequest $http) => $http->post($this->url('/chat/completions'), $payload));

        $text = $response->json('choices.0.message.content');
        if (! is_string($text)) {
            throw ChatbotProviderException::make('openai', 'Unexpected response from the OpenAI API.');
        }

        $promptTokens = $response->json('usage.prompt_tokens');
        $completionTokens = $response->json('usage.completion_tokens');

        return $this->aiResponse($text, $context, [
            'prompt_tokens' => is_numeric($promptTokens) ? (int) $promptTokens : null,
            'completion_tokens' => is_numeric($completionTokens) ? (int) $completionTokens : null,
            'estimated_cost' => $this->estimateCost($promptTokens, $completionTokens),
        ]);
    }

    /** Retrieves the configured model's metadata — a free call, no tokens used. */
    public function testConnection(): array
    {
        if ($errors = $this->configurationErrors()) {
            return ['ok' => false, 'message' => implode(' ', $errors)];
        }

        try {
            $this->send(fn (PendingRequest $http) => $http->get($this->url('/models/' . rawurlencode($this->config->model_name))));
        } catch (ChatbotProviderException $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }

        return ['ok' => true, 'message' => "Connected to OpenAI. Model \"{$this->config->model_name}\" is available. (No tokens were used.)"];
    }

    /**
     * Estimated cost in USD, only when token usage AND admin-configured prices
     * (per 1M tokens) are both available. Otherwise null — no guessing.
     */
    public function estimateCost(mixed $promptTokens, mixed $completionTokens): ?float
    {
        $in = $this->config->setting('input_price_per_million');
        $out = $this->config->setting('output_price_per_million');

        if (! is_numeric($promptTokens) || ! is_numeric($completionTokens) || ! is_numeric($in) || ! is_numeric($out)) {
            return null;
        }

        return round(($promptTokens / 1_000_000) * $in + ($completionTokens / 1_000_000) * $out, 6);
    }

    private function throttle(): void
    {
        $limits = [
            'hour' => [(int) $this->config->setting('max_requests_per_hour', 60), 3600],
            'day' => [(int) $this->config->setting('max_requests_per_day', 300), 86400],
        ];

        foreach ($limits as $period => [$max, $decay]) {
            if ($max > 0 && RateLimiter::tooManyAttempts("chatbot:openai:{$period}", $max)) {
                throw ChatbotProviderException::make('openai', "Request limit reached ({$max} per {$period}).");
            }
        }
        foreach ($limits as $period => [$max, $decay]) {
            if ($max > 0) {
                RateLimiter::hit("chatbot:openai:{$period}", $decay);
            }
        }
    }

    private function apiKey(): ?string
    {
        $key = $this->keySource() === self::KEY_SOURCE_DATABASE
            ? $this->config->api_key_encrypted
            : config('chatbot.openai.api_key');

        return filled($key) ? trim((string) $key) : null;
    }

    private function url(string $path): string
    {
        $base = filled($this->config->base_url) ? $this->config->base_url : config('chatbot.openai.base_url', 'https://api.openai.com/v1');

        return rtrim((string) $base, '/') . $path;
    }

    /** @param  callable(PendingRequest): Response  $call */
    private function send(callable $call): Response
    {
        try {
            $response = $call(
                Http::acceptJson()->withToken($this->apiKey())
                    ->timeout($this->timeout())->connectTimeout(min(10, $this->timeout()))
            );
        } catch (ConnectionException $e) {
            throw ChatbotProviderException::make('openai', str_contains($e->getMessage(), 'cURL error 28')
                ? 'The OpenAI API did not respond within ' . $this->timeout() . ' seconds.'
                : 'Could not connect to the OpenAI API.');
        }

        if ($response->successful()) {
            return $response;
        }

        // Error texts are rebuilt here on purpose: OpenAI's own 401 message
        // echoes part of the key, so it is never passed through.
        $message = match ($response->status()) {
            401 => 'OpenAI rejected the API key (HTTP 401).',
            403 => 'The API key has no access to this model or project (HTTP 403).',
            404 => "Model \"{$this->config->model_name}\" was not found (HTTP 404).",
            429 => 'OpenAI rate limit or quota exceeded (HTTP 429): ' . $this->safeError($response),
            default => 'OpenAI API error (HTTP ' . $response->status() . '): ' . $this->safeError($response),
        };

        throw ChatbotProviderException::make('openai', $message);
    }

    private function safeError(Response $response): string
    {
        $text = (string) ($response->json('error.message') ?? 'no details');
        // Strip anything that looks like a key, just in case.
        $text = preg_replace('/sk-[A-Za-z0-9_\-\*]{4,}/', 'sk-***', $text) ?? '';

        return mb_substr($text, 0, 200);
    }
}
