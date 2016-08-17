<?php

namespace App\Behaviors;

use Illuminate\Database\Eloquent\Builder;

trait Tree
{
    protected $path;

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeTree(Builder $query)
    {
        $query->parents()
            ->with('children')
            ->with('children.children')
            ->with('children.children.children');
    }

    public function scopeParents(Builder $query)
    {
        $query->where('parent_id', 0);
    }
}