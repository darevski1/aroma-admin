<?php

namespace App\Services\Chatbot\Contracts;

use App\Services\Chatbot\Exceptions\ChatbotProviderException;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;

interface ChatbotProviderInterface
{
    /** Machine key: faq | ollama | openai */
    public function key(): string;

    public function label(): string;

    /** Whether requests cost money. Paid providers are never used as a fallback. */
    public function isPaid(): bool;

    /** Whether the provider needs retrieved knowledge context and a system prompt. */
    public function usesKnowledgeContext(): bool;

    public function isEnabled(): bool;

    /** @return string[] human-readable configuration problems (empty when ready) */
    public function configurationErrors(): array;

    /** @throws ChatbotProviderException */
    public function reply(ChatContext $context): ChatbotResponse;

    /**
     * Checks connectivity / configuration without generating paid usage.
     *
     * @return array{ok: bool, message: string}
     */
    public function testConnection(): array;
}
