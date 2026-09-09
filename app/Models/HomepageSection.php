<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'content' => 'array',
    ];

    /**
     * Whether this section has admin-created content
     * (rendered through the generic custom partial).
     */
    public function hasCustomContent(): bool
    {
        return !empty($this->content);
    }

    /**
     * Get published sections ordered by sort_order.
     */
    public static function published(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_published', true)
            ->orderBy('sort_order')
            ->get();
    }
}
