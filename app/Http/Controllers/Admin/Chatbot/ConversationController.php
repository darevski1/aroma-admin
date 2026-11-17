<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'search' => 'nullable|string|max:200',
        ]);

        $query = ChatbotConversation::query()
            ->with('user:id,name,email')
            ->withCount('messages')
            ->withCount(['messages as unanswered_count' => fn ($q) => $q->where('sender', ChatbotMessage::SENDER_USER)->where('answered', false)])
            ->addSelect(['last_question' => ChatbotMessage::select('message')
                ->whereColumn('conversation_id', 'chatbot_conversations.id')
                ->where('sender', ChatbotMessage::SENDER_USER)
                ->latest('id')->limit(1)]);

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q
                ->where('visitor_name', 'like', "%{$search}%")
                ->orWhere('visitor_email', 'like', "%{$search}%")
                ->orWhereHas('messages', fn ($m) => $m->where('message', 'like', "%{$search}%")));
        }
        if ($request->filled('from')) {
            $query->where('started_at', '>=', $request->date('from')->startOfDay());
        }
        if ($request->filled('to')) {
            $query->where('started_at', '<=', $request->date('to')->endOfDay());
        }
        if ($request->boolean('unanswered')) {
            $query->whereHas('messages', fn ($m) => $m->where('sender', ChatbotMessage::SENDER_USER)->where('answered', false));
        }
        if ($request->filled('provider')) {
            $query->whereHas('messages', fn ($m) => $m->where('provider', $request->query('provider')));
        }

        return view('admin_pages.chatbot.conversations.index', [
            'conversations' => $query->latest('last_activity_at')->paginate(25)->withQueryString(),
        ]);
    }

    public function show(ChatbotConversation $conversation)
    {
        $conversation->load(['user:id,name,email', 'messages.matchedFaq:id,question']);

        return view('admin_pages.chatbot.conversations.show', compact('conversation'));
    }

    public function destroy(ChatbotConversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('chatbot.admin.conversations.index')->with('success', 'Conversation deleted.');
    }
}
