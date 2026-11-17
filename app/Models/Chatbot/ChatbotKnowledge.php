<?php

namespace App\Models\Chatbot;

use App\Services\Chatbot\Support\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Provider-independent knowledge base entry. */
class ChatbotKnowledge extends Model
{
    public const TYPES = ['text' => 'Text', 'url' => 'URL', 'document' => 'Document'];

    protected $table = 'chatbot_knowledge';

    protected $fillable = [
        'title', 'content', 'type', 'source', 'language', 'category_id', 'is_active', 'metadata',
    ];

    protected $hidden = ['search_text'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'metadata' => 'array'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $entry) {
            $entry->search_text = TextNormalizer::normalize($entry->title . ' ' . $entry->content);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChatbotCategory::class, 'category_id');
    }

    /** Active entries whose category (if any) is also active. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('category_id')
                ->orWhereHas('category', fn ($c) => $c->where('is_active', true)));
    }
}
