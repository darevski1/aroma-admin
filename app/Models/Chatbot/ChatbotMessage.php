<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    public const SENDER_USER = 'user';
    public const SENDER_BOT = 'bot';

    protected $table = 'chatbot_messages';

    protected $fillable = [
        'conversation_id', 'sender', 'message', 'provider', 'response_type', 'answered',
        'matched_faq_id', 'prompt_tokens', 'completion_tokens', 'estimated_cost', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'answered' => 'boolean',
            'metadata' => 'array',
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
            'estimated_cost' => 'float',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    public function matchedFaq(): BelongsTo
    {
        return $this->belongsTo(ChatbotFaq::class, 'matched_faq_id');
    }
}
