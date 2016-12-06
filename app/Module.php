<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name'];

    function scopeOptions(Builder $query)
    {
        return $query->orderBy('name')->pluck('name', 'id');
    }
}
