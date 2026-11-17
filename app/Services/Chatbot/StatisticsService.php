<?php

namespace App\Services\Chatbot;

use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Read-only numbers for the chatbot dashboard and usage statistics pages. */
class StatisticsService
{
    public function totals(): array
    {
        return [
            'conversations' => ChatbotConversation::count(),
            'messages' => ChatbotMessage::count(),
            'questions' => ChatbotMessage::where('sender', ChatbotMessage::SENDER_USER)->count(),
            'conversations_today' => ChatbotConversation::where('started_at', '>=', now()->startOfDay())->count(),
            'unanswered' => ChatbotMessage::where('sender', ChatbotMessage::SENDER_USER)->where('answered', false)->count(),
        ];
    }

    /** Most frequent visitor questions (grouped on the exact text, case-insensitive). */
    public function topQuestions(int $limit = 10): Collection
    {
        return ChatbotMessage::query()
            ->where('sender', ChatbotMessage::SENDER_USER)
            ->select(DB::raw('LOWER(message) as question'), DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as last_asked'))
            ->groupBy(DB::raw('LOWER(message)'))
            ->orderByDesc('total')->orderByDesc('last_asked')
            ->limit($limit)->get();
    }

    public function unansweredQuestions(int $limit = 10): Collection
    {
        return ChatbotMessage::query()
            ->where('sender', ChatbotMessage::SENDER_USER)->where('answered', false)
            ->latest('id')->limit($limit)
            ->get(['id', 'conversation_id', 'message', 'provider', 'response_type', 'created_at']);
    }

    /** Bot replies grouped by provider and response type. */
    public function byProvider(): Collection
    {
        return ChatbotMessage::query()
            ->where('sender', ChatbotMessage::SENDER_BOT)
            ->select('provider', 'response_type', DB::raw('COUNT(*) as total'))
            ->groupBy('provider', 'response_type')
            ->get()
            ->groupBy('provider')
            ->map(fn ($rows) => $rows->pluck('total', 'response_type')->map(fn ($v) => (int) $v));
    }

    public function responseTypes(): array
    {
        $counts = ChatbotMessage::query()->where('sender', ChatbotMessage::SENDER_BOT)
            ->select('response_type', DB::raw('COUNT(*) as total'))->groupBy('response_type')
            ->pluck('total', 'response_type');

        return [
            'faq' => (int) ($counts['faq'] ?? 0),
            'ai' => (int) ($counts['ai'] ?? 0),
            'fallback' => (int) ($counts['fallback'] ?? 0),
            'error' => (int) ($counts['error'] ?? 0),
        ];
    }

    /** Conversations started per day for the last $days days (zero-filled). */
    public function conversationsPerDay(int $days = 14): array
    {
        $from = CarbonImmutable::now()->subDays($days - 1)->startOfDay();

        $rows = ChatbotConversation::query()->where('started_at', '>=', $from)->get(['started_at'])
            ->countBy(fn ($c) => $c->started_at->format('Y-m-d'));

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $from->addDays($i)->format('Y-m-d');
            $series[$day] = (int) ($rows[$day] ?? 0);
        }

        return $series;
    }

    /** OpenAI usage, tracked separately from FAQ / Ollama. */
    public function openAiUsage(): array
    {
        $base = ChatbotMessage::query()->where('sender', ChatbotMessage::SENDER_BOT)->where('provider', 'openai')
            ->where('response_type', 'ai');

        $row = (clone $base)->select(
            DB::raw('COUNT(*) as requests'),
            DB::raw('SUM(prompt_tokens) as prompt_tokens'),
            DB::raw('SUM(completion_tokens) as completion_tokens'),
            DB::raw('SUM(estimated_cost) as cost'),
            DB::raw('SUM(CASE WHEN estimated_cost IS NULL THEN 1 ELSE 0 END) as unpriced'),
        )->first();

        $month = (clone $base)->where('created_at', '>=', now()->startOfMonth())
            ->select(DB::raw('COUNT(*) as requests'), DB::raw('SUM(prompt_tokens) as prompt_tokens'),
                DB::raw('SUM(completion_tokens) as completion_tokens'), DB::raw('SUM(estimated_cost) as cost'))
            ->first();

        return [
            'requests' => (int) $row->requests,
            'prompt_tokens' => (int) $row->prompt_tokens,
            'completion_tokens' => (int) $row->completion_tokens,
            'cost' => $row->cost !== null ? (float) $row->cost : null,
            'unpriced' => (int) $row->unpriced,
            'month_requests' => (int) $month->requests,
            'month_tokens' => (int) $month->prompt_tokens + (int) $month->completion_tokens,
            'month_cost' => $month->cost !== null ? (float) $month->cost : null,
        ];
    }

    public function faqMatches(int $limit = 10): Collection
    {
        return ChatbotMessage::query()
            ->where('sender', ChatbotMessage::SENDER_BOT)->whereNotNull('matched_faq_id')
            ->select('matched_faq_id', DB::raw('COUNT(*) as total'))
            ->groupBy('matched_faq_id')->orderByDesc('total')->limit($limit)
            ->with('matchedFaq:id,question')->get();
    }
}
