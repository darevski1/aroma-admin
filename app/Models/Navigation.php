<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'page_id',
        'url',
        'published',
        'position',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function subNavigations()
    {
        return $this->hasMany(SubNavigation::class)->orderBy('position');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the resolved link URL for this navigation item.
     */
    public function getLinkUrl(): string
    {
        if ($this->page_id && $this->page) {
            return $this->page->getUrl();
        }

        if ($this->url) {
            return $this->url;
        }

        return url($this->slug);
    }
}
