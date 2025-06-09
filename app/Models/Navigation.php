<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function subNavigations()
    {
        return $this->hasMany(SubNavigation::class);
    }
}
