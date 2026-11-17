<?php

namespace App\Services\Chatbot\Providers;

use App\Models\Chatbot\ChatbotProviderSetting;
use App\Services\Chatbot\Contracts\ChatbotProviderInterface;
use App\Services\Chatbot\InstructionService;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;

abstract class AbstractChatbotProvider implements ChatbotProviderInterface
{
    public function __construct(protected ChatbotProviderSetting $config)
    {
    }

    public function isPaid(): bool
    {
        return false;
    }

    public function usesKnowledgeContext(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->config->is_enabled;
    }

    public function config(): ChatbotProviderSetting
    {
        return $this->config;
    }

    /** Chat messages in the OpenAI/Ollama shape: system, history, question. */
    protected function chatMessages(ChatContext $context): array
    {
        $messages = [['role' => 'system', 'content' => $context->systemPrompt]];
        foreach ($context->history as $turn) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $context->question];

        return $messages;
    }

    /** Turn raw model text into a normalized response, honouring the no-answer marker. */
    protected function aiResponse(string $text, ChatContext $context, array $extra = []): ChatbotResponse
    {
        $text = trim($text);
        $answered = ! str_contains($text, InstructionService::NO_ANSWER_MARKER);
        $text = trim(str_replace(InstructionService::NO_ANSWER_MARKER, '', $text));

        if ($text === '') {
            $text = (string) $context->settings->fallback_message;
            $answered = false;
        }

        return new ChatbotResponse(
            message: $text,
            answered: $answered,
            type: ChatbotResponse::TYPE_AI,
            provider: $this->key(),
            promptTokens: $extra['prompt_tokens'] ?? null,
            completionTokens: $extra['completion_tokens'] ?? null,
            estimatedCost: $extra['estimated_cost'] ?? null,
            metadata: array_filter([
                'model' => $this->config->model_name,
                'context_ids' => $context->knowledge?->map(fn ($s) => $s->source . ':' . $s->id)->all(),
            ]),
        );
    }

    protected function temperature(ChatContext $context): ?float
    {
        // Provider value first, then the global default; neither set = not sent
        // (some models only accept their default temperature).
        $value = $this->config->setting('temperature', $context->settings->temperature);

        return $value === null ? null : (float) $value;
    }

    protected function maxTokens(): int
    {
        return max(16, (int) $this->config->setting('max_tokens', 500));
    }

    protected function timeout(): int
    {
        return max(1, min(300, (int) $this->config->setting('timeout', 30)));
    }
}
