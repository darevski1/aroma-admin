<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A hero slider, managed in Admin → Hero Sliders and placed on any public page
 * by writing its shortcode (e.g. [2026-1]) in the page content.
 */
class HeroSlider extends Model
{
    public const PLACEMENTS = ['top' => 'Top of the page content', 'bottom' => 'Bottom of the page content'];

    public const TRANSITIONS = ['fade' => 'Fade', 'slide' => 'Slide'];

    protected $fillable = [
        'name', 'page_id', 'placement', 'is_active', 'autoplay', 'interval_ms', 'transition', 'transition_ms',
        'show_arrows', 'show_dots', 'pause_on_hover', 'full_width', 'height_desktop', 'height_mobile',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'autoplay' => 'boolean',
        'show_arrows' => 'boolean',
        'show_dots' => 'boolean',
        'pause_on_hover' => 'boolean',
        'full_width' => 'boolean',
        'interval_ms' => 'integer',
        'transition_ms' => 'integer',
        'height_desktop' => 'integer',
        'height_mobile' => 'integer',
    ];

    protected static function booted(): void
    {
        // The shortcode needs the id, so it is written right after the insert.
        static::created(function (HeroSlider $slider) {
            if (! $slider->shortcode) {
                $slider->shortcode = ($slider->created_at ?? now())->format('Y') . '-' . $slider->id;
                $slider->saveQuietly();
            }
        });
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function slides(): HasMany
    {
        return $this->hasMany(HeroSlide::class)->orderBy('position')->orderBy('id');
    }

    public function activeSlides(): HasMany
    {
        return $this->slides()->where('is_active', true);
    }

    /** The text an admin pastes into a page, e.g. "[2026-1]". */
    public function getTagAttribute(): string
    {
        return '[' . $this->shortcode . ']';
    }
}
