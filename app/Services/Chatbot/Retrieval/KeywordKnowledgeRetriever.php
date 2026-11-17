<?php

namespace App\Services\Chatbot\Retrieval;

use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotKnowledge;
use App\Services\Chatbot\Contracts\KnowledgeRetrieverInterface;
use App\Services\Chatbot\Support\KnowledgeSnippet;
use App\Services\Chatbot\Support\TextNormalizer;
use Illuminate\Support\Collection;

/**
 * Database keyword retrieval over the knowledge base and FAQ. Entries are
 * prefiltered in SQL on their normalized `search_text`, then ranked by the
 * share of question stems they contain (title hits weigh more).
 */
class KeywordKnowledgeRetriever implements KnowledgeRetrieverInterface
{
    public function retrieve(string $query, ?string $language = null, int $limit = 4): Collection
    {
        $stems = TextNormalizer::stems($query);
        if (! $stems) {
            return collect();
        }

        $snippets = collect();

        foreach ($this->candidates(ChatbotKnowledge::query()->active(), $stems) as $entry) {
            $score = $this->score($stems, $entry->title, $entry->search_text, $entry->language, $language);
            if ($score > 0) {
                $snippets->push(new KnowledgeSnippet('knowledge', $entry->id, $entry->title, $entry->content, $score));
            }
        }

        foreach ($this->candidates(ChatbotFaq::query()->active(), $stems) as $faq) {
            $score = $this->score($stems, $faq->question . ' ' . implode(' ', $faq->keywords ?? []), $faq->search_text, $faq->language, $language);
            if ($score > 0) {
                $snippets->push(new KnowledgeSnippet('faq', $faq->id, $faq->question, $faq->answer, $score));
            }
        }

        return $snippets->sortByDesc('score')->take($limit)->values();
    }

    private function candidates($query, array $stems): Collection
    {
        return $query->where(function ($q) use ($stems) {
            foreach (array_slice($stems, 0, 12) as $stem) {
                $q->orWhere('search_text', 'like', '%' . $stem . '%');
            }
        })->limit(200)->get();
    }

    private function score(array $stems, string $title, ?string $searchText, ?string $entryLanguage, ?string $language): float
    {
        $titleStems = TextNormalizer::stems($title);
        $text = ' ' . ($searchText ?? '') . ' ';
        $hits = 0.0;

        foreach ($stems as $stem) {
            if (in_array($stem, $titleStems, true)) {
                $hits += 1.5;
            } elseif (str_contains($text, ' ' . $stem)) {
                $hits += 1.0;
            }
        }

        // Require a meaningful share of the question to be covered.
        $coverage = $hits / (count($stems) * 1.5);
        if ($coverage < 0.2) {
            return 0.0;
        }
        if ($language && $entryLanguage && $entryLanguage !== $language) {
            $coverage *= 0.9;
        }

        return round(min(1.0, $coverage), 4);
    }
}
