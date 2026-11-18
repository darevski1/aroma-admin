<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteImageSlot extends Model
{
    protected $fillable = ['path', 'src_key', 'kind', 'alt', 'context', 'page_title', 'last_seen_at'];

    protected $casts = ['last_seen_at' => 'datetime'];
}
