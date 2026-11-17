<?php

namespace App\Services\Chatbot\Support;

use App\Models\Chatbot\ChatbotSetting;
use Illuminate\Support\Collection;

/** Everything a provider needs to answer one visitor question. */
final class ChatContext
{
    /**
     * @param  array<int, array{role: string, content: string}>  $history  previous turns, oldest first
     * @param  Collection<int, KnowledgeSnippet>  $knowledge  retrieved context (AI providers only)
     */
    public function __construct(
        public readonly string $question,
        public readonly string $language,
        public readonly ChatbotSetting $settings,
        public readonly array $history = [],
        public readonly ?Collection $knowledge = null,
        public readonly string $systemPrompt = '',
    ) {
    }
}
