<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Page extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'featured_image',
        'layout',
        'route_name',
        'published',
        'position',
        'settings',
    ];

    protected $casts = [
        'published' => 'boolean',
        'settings'  => 'array',
    ];

    /**
     * Get a setting value with a fallback default.
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Get navigation items linked to this page.
     */
    public function navigations()
    {
        return $this->hasMany(Navigation::class);
    }

    /**
     * Check if this page appears in the public navigation.
     */
    public function isInNavigation(): bool
    {
        return $this->navigations()->exists();
    }

    /**
     * Get the public URL for this page.
     */
    public function getUrl(): string
    {
        if ($this->layout === 'welcome') {
            return url('/');
        }

        return url('pages/' . $this->slug);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif', 'image/svg+xml'])
            ->maxFileSize(10 * 1024 * 1024);

        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes([
                'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'application/zip',
            ])
            ->maxFileSize(20 * 1024 * 1024);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->nonQueued();

        $this->addMediaConversion('small')
            ->width(400)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)
            ->nonQueued();

        $this->addMediaConversion('social')
            ->width(1200)
            ->height(630)
            ->fit('contain', 1200, 630)
            ->nonQueued();
    }
}
