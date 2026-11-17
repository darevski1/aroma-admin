<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotKnowledge;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\StatisticsService;

class DashboardController extends Controller
{
    public function index(StatisticsService $stats, ChatbotManager $manager)
    {
        $settings = ChatbotSetting::current();
        $active = $manager->active($settings);

        return view('admin_pages.chatbot.dashboard', [
            'settings' => $settings,
            'totals' => $stats->totals(),
            'topQuestions' => $stats->topQuestions(8),
            'unanswered' => $stats->unansweredQuestions(8),
            'activeProvider' => $active,
            'providerProblems' => $manager->problems($active->key()),
            'faqCount' => ChatbotFaq::where('is_active', true)->count(),
            'knowledgeCount' => ChatbotKnowledge::where('is_active', true)->count(),
        ]);
    }
}
