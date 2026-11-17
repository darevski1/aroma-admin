<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin_pages.chatbot.categories.index', [
            'categories' => ChatbotCategory::ordered()->withCount(['faqs', 'knowledge'])->get(),
        ]);
    }

    public function create()
    {
        return view('admin_pages.chatbot.categories.form', [
            'category' => new ChatbotCategory(['is_active' => true, 'sort_order' => (ChatbotCategory::max('sort_order') ?? 0) + 1]),
        ]);
    }

    public function store(Request $request)
    {
        ChatbotCategory::create($this->validated($request));

        return redirect()->route('chatbot.admin.categories.index')->with('success', 'Category added.');
    }

    public function edit(ChatbotCategory $category)
    {
        return view('admin_pages.chatbot.categories.form', compact('category'));
    }

    public function update(Request $request, ChatbotCategory $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('chatbot.admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ChatbotCategory $category)
    {
        // FAQs and knowledge entries stay; their category is cleared (FK nullOnDelete).
        $category->delete();

        return redirect()->route('chatbot.admin.categories.index')->with('success', 'Category deleted. Its FAQ and knowledge entries were kept without a category.');
    }

    public function toggle(ChatbotCategory $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        return response()->json(['success' => true, 'is_active' => $category->is_active]);
    }

    private function validated(Request $request, ?ChatbotCategory $category = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('chatbot_categories', 'slug')->ignore($category?->id)],
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
