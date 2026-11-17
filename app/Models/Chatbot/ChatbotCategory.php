<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatbotCategory extends Model
{
    protected $table = 'chatbot_categories';

    protected $fillable = ['name', 'slug', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $category) {
            if (blank($category->slug)) {
                $base = Str::slug(Str::transliterate($category->name)) ?: Str::lower(Str::random(8));
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->whereKeyNot($category->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $category->slug = $slug;
            }
        });
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ChatbotFaq::class, 'category_id');
    }

    public function knowledge(): HasMany
    {
        return $this->hasMany(ChatbotKnowledge::class, 'category_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
