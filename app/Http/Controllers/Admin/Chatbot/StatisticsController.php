<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Services\Chatbot\StatisticsService;

class StatisticsController extends Controller
{
    public function index(StatisticsService $stats)
    {
        $openai = ChatbotProviderSetting::for('openai');

        return view('admin_pages.chatbot.statistics', [
            'totals' => $stats->totals(),
            'types' => $stats->responseTypes(),
            'byProvider' => $stats->byProvider(),
            'perDay' => $stats->conversationsPerDay(14),
            'openai' => $stats->openAiUsage(),
            'pricingConfigured' => is_numeric($openai->setting('input_price_per_million')) && is_numeric($openai->setting('output_price_per_million')),
            'faqMatches' => $stats->faqMatches(10),
            'unanswered' => $stats->unansweredQuestions(20),
        ]);
    }
}
