<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/** One slide of a {@see HeroSlider}. Images live on the "public" disk. */
class HeroSlide extends Model
{
    public const IMAGE_POSITIONS = [
        'center center' => 'Center',
        'center top' => 'Top',
        'center bottom' => 'Bottom',
        'left center' => 'Left',
        'right center' => 'Right',
        'left top' => 'Top left',
        'right top' => 'Top right',
        'left bottom' => 'Bottom left',
        'right bottom' => 'Bottom right',
    ];

    public const TEXT_ALIGNMENTS = ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'];

    protected $fillable = [
        'hero_slider_id', 'position', 'is_active', 'title', 'subtitle', 'description',
        'button_text', 'button_url', 'button_new_tab', 'link_whole_slide',
        'desktop_image', 'desktop_width', 'desktop_height',
        'mobile_image', 'mobile_width', 'mobile_height',
        'image_alt', 'image_position', 'text_alignment', 'overlay_opacity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'button_new_tab' => 'boolean',
        'link_whole_slide' => 'boolean',
        'position' => 'integer',
        'overlay_opacity' => 'integer',
        'desktop_width' => 'integer',
        'desktop_height' => 'integer',
        'mobile_width' => 'integer',
        'mobile_height' => 'integer',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(HeroSlider::class, 'hero_slider_id');
    }

    public function getDesktopUrlAttribute(): ?string
    {
        return $this->desktop_image ? Storage::disk('public')->url($this->desktop_image) : null;
    }

    public function getMobileUrlAttribute(): ?string
    {
        return $this->mobile_image ? Storage::disk('public')->url($this->mobile_image) : null;
    }

    public function hasButton(): bool
    {
        return filled($this->button_text) && filled($this->button_url);
    }
}
