<?php

namespace App\Services\Chatbot\Support;

use App\Models\Chatbot\ChatbotFaq;
use Illuminate\Support\Collection;

/**
 * Scores active FAQ entries against a visitor question. Free, database-only.
 *
 * Score (0..1) is the best of:
 *  - exact match of the normalized question                       → 1.0
 *  - one normalized question contained in the other (3+ words)    → 0.9
 *  - word overlap (Dice coefficient on stems)                     → 0..1
 *  - keyword hits (when keyword matching is on)                   → 0.6 + 0.15 per extra hit
 * Entries in another language than the visitor's get a small penalty when
 * language matching is on. Below the threshold nothing is returned, so a
 * random FAQ answer is never given.
 */
class FaqMatcher
{
    public const DEFAULT_THRESHOLD = 0.5;

    /**
     * @return array{faq: ChatbotFaq, score: float}|null
     */
    public function bestMatch(string $question, array $options = []): ?array
    {
        $ranked = $this->rank($question, $options, 1);

        return $ranked->first();
    }

    /**
     * @return Collection<int, array{faq: ChatbotFaq, score: float}>
     */
    public function rank(string $question, array $options = [], int $limit = 5): Collection
    {
        $threshold = (float) ($options['threshold'] ?? self::DEFAULT_THRESHOLD);
        $useKeywords = (bool) ($options['keyword_matching'] ?? true);
        $useLanguage = (bool) ($options['language_matching'] ?? true);
        $language = $options['language'] ?? TextNormalizer::detectLanguage($question);

        $normalized = TextNormalizer::normalize($question);
        $stems = TextNormalizer::stems($question);
        if ($normalized === '') {
            return collect();
        }

        return $this->candidates($stems, $normalized)
            ->map(function (ChatbotFaq $faq) use ($normalized, $stems, $useKeywords, $useLanguage, $language) {
                $score = $this->score($faq, $normalized, $stems, $useKeywords);
                if ($useLanguage && $faq->language && $faq->language !== $language) {
                    $score *= 0.9;
                }

                return ['faq' => $faq, 'score' => round($score, 4)];
            })
            ->filter(fn ($row) => $row['score'] >= $threshold)
            ->sortBy([['score', 'desc'], [fn ($row) => $row['faq']->sort_order, 'asc']])
            ->take($limit)
            ->values();
    }

    private function score(ChatbotFaq $faq, string $normalized, array $stems, bool $useKeywords): float
    {
        $faqQuestion = TextNormalizer::normalize($faq->question);
        if ($faqQuestion === $normalized) {
            return 1.0;
        }

        $best = 0.0;

        $shorter = strlen($faqQuestion) < strlen($normalized) ? $faqQuestion : $normalized;
        $longer = $shorter === $faqQuestion ? $normalized : $faqQuestion;
        if (substr_count($shorter, ' ') >= 2 && str_contains(" $longer ", " $shorter ")) {
            $best = 0.9;
        }

        $faqStems = TextNormalizer::stems($faq->question);
        if ($stems && $faqStems) {
            $common = count(array_intersect($stems, $faqStems));
            $best = max($best, (2 * $common) / (count($stems) + count($faqStems)));
        }

        if ($useKeywords) {
            $hits = 0;
            foreach ($faq->keywords ?? [] as $keyword) {
                $kw = TextNormalizer::normalize($keyword);
                if ($kw === '') {
                    continue;
                }
                $kwStems = TextNormalizer::stems($keyword) ?: [$kw];
                $phraseHit = str_contains(" $normalized ", " $kw ");
                $stemHit = ! array_diff($kwStems, $stems);
                if ($phraseHit || $stemHit) {
                    $hits++;
                }
            }
            if ($hits > 0) {
                $best = max($best, min(1.0, 0.6 + 0.15 * ($hits - 1)));
            }
        }

        return $best;
    }

    /** Active FAQs sharing at least one stem with the question (database prefilter). */
    private function candidates(array $stems, string $normalized): Collection
    {
        $query = ChatbotFaq::query()->active();
        $terms = $stems ?: array_filter(explode(' ', $normalized));

        $query->where(function ($q) use ($terms) {
            foreach (array_slice($terms, 0, 12) as $term) {
                $q->orWhere('search_text', 'like', '%' . $term . '%');
            }
        });

        return $query->ordered()->limit(300)->get();
    }
}
