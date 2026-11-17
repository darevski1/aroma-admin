<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotCategory;
use App\Models\Chatbot\ChatbotKnowledge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatbotKnowledge::query()->with('category:id,name');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->query('language'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->query('status') === 'active');
        }

        return view('admin_pages.chatbot.knowledge.index', [
            'entries' => $query->latest('updated_at')->paginate(20)->withQueryString(),
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin_pages.chatbot.knowledge.form', [
            'entry' => new ChatbotKnowledge(['is_active' => true, 'type' => 'text', 'language' => 'mk']),
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        ChatbotKnowledge::create($this->validated($request));

        return redirect()->route('chatbot.admin.knowledge.index')->with('success', 'Knowledge entry added.');
    }

    public function edit(ChatbotKnowledge $knowledge)
    {
        return view('admin_pages.chatbot.knowledge.form', [
            'entry' => $knowledge,
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, ChatbotKnowledge $knowledge)
    {
        $knowledge->update($this->validated($request));

        return redirect()->route('chatbot.admin.knowledge.index')->with('success', 'Knowledge entry updated.');
    }

    public function destroy(ChatbotKnowledge $knowledge)
    {
        $knowledge->delete();

        return redirect()->route('chatbot.admin.knowledge.index')->with('success', 'Knowledge entry deleted.');
    }

    public function toggle(ChatbotKnowledge $knowledge)
    {
        $knowledge->update(['is_active' => ! $knowledge->is_active]);

        return response()->json(['success' => true, 'is_active' => $knowledge->is_active]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:50000',
            'type' => ['required', Rule::in(array_keys(ChatbotKnowledge::TYPES))],
            'source' => 'nullable|string|max:255',
            'language' => 'required|in:mk,en',
            'category_id' => 'nullable|integer|exists:chatbot_categories,id',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
