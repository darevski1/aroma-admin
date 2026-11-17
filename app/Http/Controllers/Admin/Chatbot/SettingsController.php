<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin_pages.chatbot.settings', ['settings' => ChatbotSetting::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bot_name' => 'required|string|max:100',
            'welcome_message' => 'nullable|string|max:2000',
            'default_language' => 'required|in:mk,en',
            'fallback_message' => 'required|string|max:2000',
            'unavailable_message' => 'required|string|max:2000',
            'response_limit' => 'required|integer|min:100|max:10000',
        ]);

        $data['enabled'] = $request->boolean('enabled');
        $data['show_on_frontend'] = $request->boolean('show_on_frontend');
        $data['fallback_to_faq'] = $request->boolean('fallback_to_faq');

        ChatbotSetting::current()->update($data);

        return redirect()->route('chatbot.admin.settings')->with('success', 'Chatbot settings saved.');
    }
}
