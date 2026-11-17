<?php

namespace App\Models\Chatbot;

use App\Services\Chatbot\Support\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotFaq extends Model
{
    protected $table = 'chatbot_faqs';

    protected $fillable = [
        'category_id', 'question', 'answer', 'keywords', 'language', 'is_active', 'sort_order',
    ];

    protected $hidden = ['search_text'];

    protected function casts(): array
    {
        return ['keywords' => 'array', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $faq) {
            $faq->search_text = TextNormalizer::normalize(
                $faq->question . ' ' . implode(' ', $faq->keywords ?? []) . ' ' . $faq->answer
            );
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

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
