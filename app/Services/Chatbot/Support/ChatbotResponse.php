<?php

namespace App\Services\Chatbot\Support;

/** Normalized reply returned by every provider. */
final class ChatbotResponse
{
    public const TYPE_FAQ = 'faq';
    public const TYPE_AI = 'ai';
    public const TYPE_FALLBACK = 'fallback';
    public const TYPE_ERROR = 'error';

    public function __construct(
        public string $message,
        public bool $answered,
        public string $type,
        public string $provider,
        public ?int $matchedFaqId = null,
        public ?int $promptTokens = null,
        public ?int $completionTokens = null,
        public ?float $estimatedCost = null,
        public array $metadata = [],
    ) {
    }
}
