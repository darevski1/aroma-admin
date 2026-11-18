<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteImageOverride extends Model
{
    public const GLOBAL = '*';

    protected $fillable = [
        'scope_path', 'src_key', 'media_id', 'mobile_media_id',
        'alt', 'title', 'object_position', 'object_fit', 'hidden',
    ];

    protected $casts = ['hidden' => 'boolean'];

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function mobileMedia()
    {
        return $this->belongsTo(Media::class, 'mobile_media_id');
    }

    public function isGlobal(): bool
    {
        return $this->scope_path === self::GLOBAL;
    }
}
