<?php

namespace App\Services\Chatbot;

use App\Models\Chatbot\ChatbotProviderSetting;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\Chatbot\Contracts\ChatbotProviderInterface;
use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use App\Services\Chatbot\Support\TextNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Picks the provider selected by the administrator and runs one question
 * through it. All providers share the same instructions, FAQ and knowledge
 * base. When the active AI provider fails, the manager may fall back to the
 * free FAQ Engine (if allowed in settings) — never to a paid provider.
 */
class ChatbotManager
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledge,
        private readonly InstructionService $instructions,
    ) {
    }

    /** @return array<string, string> key => class */
    public function available(): array
    {
        return config('chatbot.providers', []);
    }

    public function provider(string $key): ChatbotProviderInterface
    {
        $class = $this->available()[$key] ?? throw new InvalidArgumentException("Unknown chatbot provider [{$key}].");

        // Built fresh each time so admin changes to provider settings apply immediately.
        return app()->make($class, ['config' => ChatbotProviderSetting::for($key)]);
    }

    public function activeKey(?ChatbotSetting $settings = null): string
    {
        $key = ($settings ?? ChatbotSetting::current())->provider;

        return array_key_exists($key, $this->available()) ? $key : 'faq';
    }

    public function active(?ChatbotSetting $settings = null): ChatbotProviderInterface
    {
        return $this->provider($this->activeKey($settings));
    }

    /** Why a provider cannot be used right now (empty array = ready). */
    public function problems(string|ChatbotProviderInterface $provider): array
    {
        $provider = is_string($provider) ? $this->provider($provider) : $provider;
        $problems = $provider->configurationErrors();
        if (! $provider->isEnabled()) {
            array_unshift($problems, $provider->label() . ' is disabled.');
        }

        return $problems;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function respond(string $question, array $history = [], ?ChatbotSetting $settings = null): ChatbotResponse
    {
        $settings ??= ChatbotSetting::current();
        $language = TextNormalizer::detectLanguage($question);
        $provider = $this->active($settings);

        try {
            if ($problems = $this->problems($provider)) {
                throw ChatbotProviderException::make($provider->key(), implode(' ', $problems));
            }
            $response = $provider->reply($this->context($provider, $question, $language, $history, $settings));
        } catch (ChatbotProviderException $e) {
            // Only the admin-readable message is logged — never request data or credentials.
            Log::warning('Chatbot provider unavailable: ' . $e->getMessage());
            $response = $this->fallback($provider, $question, $language, $settings, $e->getMessage());
        }

        $response->message = $this->limit($response->message, (int) $settings->response_limit);

        return $response;
    }

    private function fallback(ChatbotProviderInterface $failed, string $question, string $language, ChatbotSetting $settings, string $reason): ChatbotResponse
    {
        $faq = $this->provider('faq');

        // The free FAQ Engine is the only possible fallback; a paid provider never is.
        if ($failed->key() !== 'faq' && $settings->fallback_to_faq && ! $faq->isPaid() && $faq->isEnabled()) {
            $response = $faq->reply(new ChatContext($question, $language, $settings));
            $response->metadata += ['fallback_from' => $failed->key(), 'error' => $reason];

            return $response;
        }

        return new ChatbotResponse(
            message: (string) ($settings->unavailable_message ?: 'The assistant is temporarily unavailable. Please try again later.'),
            answered: false,
            type: ChatbotResponse::TYPE_ERROR,
            provider: $failed->key(),
            metadata: ['error' => $reason],
        );
    }

    private function context(ChatbotProviderInterface $provider, string $question, string $language, array $history, ChatbotSetting $settings): ChatContext
    {
        if (! $provider->usesKnowledgeContext()) {
            return new ChatContext($question, $language, $settings, $history);
        }

        $snippets = $this->knowledge->relevant($question, $language);
        $prompt = $this->instructions->systemPrompt($settings, $this->knowledge->formatContext($snippets));

        return new ChatContext($question, $language, $settings, $history, $snippets, $prompt);
    }

    private function limit(string $message, int $limit): string
    {
        return $limit > 0 && mb_strlen($message) > $limit ? rtrim(Str::limit($message, $limit - 1, '')) . '…' : $message;
    }
}
