<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'page_id',
        'url',
        'published',
        'position',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Whether this item may be shown on the public site: it is active and,
     * when it links to a page, that page is active too.
     */
    public function isVisible(): bool
    {
        if (!$this->published) {
            return false;
        }

        return !$this->page_id || ($this->page && $this->page->published);
    }

    /**
     * Build the public navigation tree. Only visible items whose whole
     * ancestor chain is visible are returned; each item exposes its
     * visible children through the `visibleChildren` relation.
     */
    public static function publicTree(): Collection
    {
        $visible = static::with('page')
            ->orderBy('position')->orderBy('id')
            ->get()
            ->filter(fn (self $item) => $item->isVisible());

        $byParent = $visible->groupBy(fn (self $item) => $item->parent_id ?? 0);

        $attach = function (self $item) use (&$attach, $byParent) {
            $item->setRelation('visibleChildren', ($byParent->get($item->id) ?? collect())->each($attach)->values());
        };

        $roots = $byParent->get(0, collect())->each($attach);

        return new Collection($roots->values()->all());
    }

    /**
     * Get the resolved link URL for this navigation item.
     */
    public function getLinkUrl(): string
    {
        if ($this->page_id && $this->page) {
            if ($this->page->layout === 'welcome') {
                return $this->page->getUrl();
            }

            return $this->page->route_name && \Illuminate\Support\Facades\Route::has($this->page->route_name)
                ? route($this->page->route_name)
                : $this->page->getUrl();
        }

        if ($this->url) {
            return $this->url;
        }

        return url($this->slug);
    }
}
