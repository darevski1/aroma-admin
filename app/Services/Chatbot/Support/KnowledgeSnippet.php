<?php

namespace App\Services\Chatbot\Support;

/** One piece of retrieved context (knowledge entry or FAQ) sent to an AI provider. */
final class KnowledgeSnippet
{
    public function __construct(
        public readonly string $source,   // 'knowledge' | 'faq'
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly float $score,
    ) {
    }
}
