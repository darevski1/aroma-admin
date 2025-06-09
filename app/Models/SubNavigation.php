<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubNavigation extends Model
{
    protected $fillable = [
        'sub_name',
        'navigation_id',
        'slug',
    ];

    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }
}
