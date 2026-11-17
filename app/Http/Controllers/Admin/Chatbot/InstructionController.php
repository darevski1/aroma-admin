<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\Chatbot\InstructionService;
use Illuminate\Http\Request;

class InstructionController extends Controller
{
    public function __construct(private readonly InstructionService $instructions)
    {
    }

    public function edit()
    {
        $settings = ChatbotSetting::current();

        return view('admin_pages.chatbot.instructions', [
            'sections' => $this->instructions->sections($settings),
            'labels' => InstructionService::SECTIONS,
            'preview' => $this->instructions->systemPrompt($settings, '(relevant knowledge base entries are inserted here for each question)'),
            'temperature' => $settings->temperature,
        ]);
    }

    public function update(Request $request)
    {
        $rules = ['temperature' => 'nullable|numeric|min:0|max:2'];
        foreach (array_keys(InstructionService::SECTIONS) as $key) {
            $rules["sections.{$key}"] = 'nullable|string|max:5000';
        }
        $data = $request->validate($rules);

        $settings = ChatbotSetting::current();
        $this->instructions->save($settings, $data['sections'] ?? []);
        $settings->update(['temperature' => $data['temperature'] ?? null]);

        return redirect()->route('chatbot.admin.instructions')->with('success', 'System instructions saved.');
    }

    public function reset()
    {
        $this->instructions->save(ChatbotSetting::current(), InstructionService::defaultSections());

        return redirect()->route('chatbot.admin.instructions')->with('success', 'Default instructions restored.');
    }
}
