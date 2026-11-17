<?php

namespace App\Services\Chatbot;

use App\Services\Chatbot\Contracts\KnowledgeRetrieverInterface;
use App\Services\Chatbot\Support\KnowledgeSnippet;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Retrieval step shared by the AI providers: finds the most relevant
 * knowledge/FAQ entries for a question and formats them as compact context.
 * Only relevant entries are sent — never the whole knowledge base.
 */
class KnowledgeBaseService
{
    public function __construct(private readonly KnowledgeRetrieverInterface $retriever)
    {
    }

    /** @return Collection<int, KnowledgeSnippet> */
    public function relevant(string $question, ?string $language = null): Collection
    {
        $max = (int) config('chatbot.retrieval.max_entries', 4);
        $perEntry = (int) config('chatbot.retrieval.max_chars_per_entry', 1500);
        $total = (int) config('chatbot.retrieval.max_total_chars', 6000);

        $used = 0;

        return $this->retriever->retrieve($question, $language, $max)
            ->map(fn (KnowledgeSnippet $s) => new KnowledgeSnippet(
                $s->source, $s->id, $s->title, Str::limit(trim($s->content), $perEntry), $s->score
            ))
            ->filter(function (KnowledgeSnippet $s) use (&$used, $total) {
                $used += mb_strlen($s->title) + mb_strlen($s->content);

                return $used <= $total;
            })
            ->values();
    }

    /** Plain-text context block for the AI prompt. */
    public function formatContext(Collection $snippets): string
    {
        return $snippets->values()->map(
            fn (KnowledgeSnippet $s, $i) => '[' . ($i + 1) . '] ' . $s->title . "\n" . $s->content
        )->implode("\n\n");
    }
}
