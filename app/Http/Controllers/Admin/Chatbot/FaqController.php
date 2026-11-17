<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotCategory;
use App\Models\Chatbot\ChatbotFaq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatbotFaq::query()->with('category:id,name');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%"));
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

        return view('admin_pages.chatbot.faqs.index', [
            'faqs' => $query->ordered()->paginate(25)->withQueryString(),
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin_pages.chatbot.faqs.form', [
            'faq' => new ChatbotFaq([
                'is_active' => true, 'language' => 'mk',
                'sort_order' => (ChatbotFaq::max('sort_order') ?? 0) + 1,
            ]),
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        ChatbotFaq::create($this->validated($request));

        return redirect()->route('chatbot.admin.faqs.index')->with('success', 'FAQ added.');
    }

    public function edit(ChatbotFaq $faq)
    {
        return view('admin_pages.chatbot.faqs.form', [
            'faq' => $faq,
            'categories' => ChatbotCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, ChatbotFaq $faq)
    {
        $faq->update($this->validated($request));

        return redirect()->route('chatbot.admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(ChatbotFaq $faq)
    {
        $faq->delete();

        return redirect()->route('chatbot.admin.faqs.index')->with('success', 'FAQ deleted.');
    }

    public function toggle(ChatbotFaq $faq)
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return response()->json(['success' => true, 'is_active' => $faq->is_active]);
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:chatbot_faqs,id',
        ]);

        $query = ChatbotFaq::whereIn('id', $data['ids']);
        $count = count($data['ids']);

        // Per-model saves keep the normalized search text in sync.
        match ($data['action']) {
            'activate' => $query->get()->each->update(['is_active' => true]),
            'deactivate' => $query->get()->each->update(['is_active' => false]),
            'delete' => $query->get()->each->delete(),
        };

        return back()->with('success', "{$count} FAQ " . ($count === 1 ? 'entry' : 'entries') . ' updated.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => 'required|string|max:1000',
            'answer' => 'required|string|max:10000',
            'keywords' => 'nullable|string|max:1000',
            'category_id' => 'nullable|integer|exists:chatbot_categories,id',
            'language' => 'required|in:mk,en',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $data['keywords'] = collect(preg_split('/[,\n;]+/', (string) ($data['keywords'] ?? '')))
            ->map(fn ($k) => trim($k))->filter()->unique()->values()->all();
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
