<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A CenoBots project shown in the "Проекти" slider on /services/cenobots.
 * The table keeps its original name; each row is one project with its client
 * logo and optional photo, both picked from the Media Library.
 */
class CenobotsClient extends Model
{
    protected $fillable = [
        'name', 'category', 'logo_media_id', 'logo_alt', 'photo_media_id', 'website_url',
        'description', 'foot_text', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Active projects in display order, for the public slider. */
    public static function forCarousel()
    {
        return static::with(['logo', 'photo'])
            ->where('is_active', true)
            ->ordered()
            ->get();
    }
}
