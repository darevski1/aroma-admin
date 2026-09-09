<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubNavigation extends Model
{
    protected $fillable = [
        'sub_name',
        'navigation_id',
        'slug',
        'page_id',
        'url',
        'published',
        'position',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the resolved link URL for this sub-navigation item.
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
