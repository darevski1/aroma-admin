<?php

namespace App\Http\Controllers;

use App\Models\Chatbot\ChatbotSetting;
use App\Services\Chatbot\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public chatbot endpoints used by the website widget. Responses are the same
 * whichever provider is active and never contain provider configuration,
 * instructions or credentials.
 */
class ChatbotController extends Controller
{
    public function __construct(private readonly ConversationService $conversations)
    {
    }

    public function message(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:' . (int) config('chatbot.max_message_length', 1000)],
        ]);

        if ($unavailable = $this->unavailable()) {
            return $unavailable;
        }

        $question = trim(preg_replace('/\s+/u', ' ', strip_tags($data['message'])) ?? '');
        if ($question === '') {
            return response()->json(['success' => false, 'message' => 'Please enter a message.'], 422);
        }

        $conversation = $this->conversations->currentOrCreate($request);
        [$botMessage, $response] = $this->conversations->ask($conversation, $question);

        return response()->json([
            'success' => $response->type !== 'error',
            'message' => $botMessage->message,
            'conversation_id' => $conversation->id,
        ], $response->type === 'error' ? 503 : 200);
    }

    public function history(Request $request): JsonResponse
    {
        if ($unavailable = $this->unavailable()) {
            return $unavailable;
        }

        $conversation = $this->conversations->current($request);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation?->id,
            'messages' => $this->conversations->publicHistory($conversation),
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $this->conversations->reset($request);

        return response()->json(['success' => true, 'conversation_id' => null]);
    }

    private function unavailable(): ?JsonResponse
    {
        if (ChatbotSetting::current()->enabled) {
            return null;
        }

        return response()->json(['success' => false, 'message' => 'The chatbot is currently disabled.'], 503);
    }
}
