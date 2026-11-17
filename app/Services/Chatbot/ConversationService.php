<?php

namespace App\Services\Chatbot;

use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use App\Services\Chatbot\Support\ChatbotResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Visitor conversations. Ownership is a random token stored only in the
 * visitor's server-side session, so one visitor can never load another's
 * history (conversation ids from the client are never trusted).
 */
class ConversationService
{
    public const SESSION_KEY = 'chatbot.conversation_token';

    public function __construct(private readonly ChatbotManager $manager)
    {
    }

    public function current(Request $request): ?ChatbotConversation
    {
        $token = $request->session()->get(self::SESSION_KEY);

        return is_string($token) && $token !== ''
            ? ChatbotConversation::where('session_id', $token)->first()
            : null;
    }

    public function currentOrCreate(Request $request): ChatbotConversation
    {
        if ($conversation = $this->current($request)) {
            if ($request->user() && ! $conversation->user_id) {
                $conversation->update(['user_id' => $request->user()->id]);
            }

            return $conversation;
        }

        $token = Str::random(48);
        $request->session()->put(self::SESSION_KEY, $token);

        return ChatbotConversation::create([
            'session_id' => $token,
            'user_id' => $request->user()?->id,
            'visitor_name' => $request->user()?->name,
            'visitor_email' => $request->user()?->email,
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    /** Starts a fresh conversation for this visitor; the old one stays for admins. */
    public function reset(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    /** Stores the question, asks the active provider, stores the reply. */
    public function ask(ChatbotConversation $conversation, string $question): array
    {
        $history = $this->history($conversation);

        $userMessage = $conversation->messages()->create([
            'sender' => ChatbotMessage::SENDER_USER,
            'message' => $question,
        ]);

        $response = $this->manager->respond($question, $history);

        $botMessage = DB::transaction(function () use ($conversation, $userMessage, $response) {
            $userMessage->update([
                'provider' => $response->provider,
                'response_type' => $response->type,
                'answered' => $response->answered,
                'matched_faq_id' => $response->matchedFaqId,
            ]);

            $bot = $conversation->messages()->create([
                'sender' => ChatbotMessage::SENDER_BOT,
                'message' => $response->message,
                'provider' => $response->provider,
                'response_type' => $response->type,
                'answered' => $response->answered,
                'matched_faq_id' => $response->matchedFaqId,
                'prompt_tokens' => $response->promptTokens,
                'completion_tokens' => $response->completionTokens,
                'estimated_cost' => $response->estimatedCost,
                'metadata' => $response->metadata ?: null,
            ]);

            $conversation->update(['last_activity_at' => now()]);

            return $bot;
        });

        return [$botMessage, $response];
    }

    /** Recent turns sent to AI providers for continuity (text only, no visitor data). */
    private function history(ChatbotConversation $conversation): array
    {
        $limit = (int) config('chatbot.retrieval.history_messages', 6);
        if ($limit <= 0) {
            return [];
        }

        return $conversation->messages()->reorder()->orderByDesc('id')->limit($limit)->get(['sender', 'message', 'response_type'])
            ->reverse()
            ->filter(fn (ChatbotMessage $m) => $m->response_type !== ChatbotResponse::TYPE_ERROR)
            ->map(fn (ChatbotMessage $m) => [
                'role' => $m->sender === ChatbotMessage::SENDER_USER ? 'user' : 'assistant',
                'content' => $m->message,
            ])->values()->all();
    }

    /** Public, safe representation of the visitor's own messages. */
    public function publicHistory(?ChatbotConversation $conversation): array
    {
        if (! $conversation) {
            return [];
        }

        return $conversation->messages()->limit(200)->get(['id', 'sender', 'message', 'created_at'])
            ->map(fn (ChatbotMessage $m) => [
                'sender' => $m->sender,
                'message' => $m->message,
                'created_at' => $m->created_at?->toIso8601String(),
            ])->all();
    }
}
