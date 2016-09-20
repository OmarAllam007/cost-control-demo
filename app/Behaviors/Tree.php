<?php

namespace App\Behaviors;

use Illuminate\Database\Eloquent\Builder;

trait Tree
{
    protected $path;


    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children()
    {
        $relation = $this->hasMany(static::class, 'parent_id');
        if (isset($this->orderByCode) && isset($this->orderByName)) {
            $relation->orderBy($this->orderByCode)->orderBy($this->orderByName);
        }
        return $relation;
    }

    public function scopeTree(Builder $query)
    {
        $query->parents()
            ->with('children')
            ->with('children.children')
            ->with('children.children.children');
        if (isset($this->orderByCode) && isset($this->orderByName)) {
            $query->orderBy($this->orderByCode)->orderBy($this->orderByName);
        }
    }

    public function scopeParents(Builder $query)
    {
        $query->where('parent_id', 0);
    }


    public function getPathAttribute()
    {
        if ($this->path) {
            return $this->path;
        }

        $stack = collect([$this->name]);
        $parent = $this->parent;
        while ($parent) {
            $stack->push($parent->name);
            $parent = $parent->parent;
        }

        return $this->path = $stack->reverse()->implode(' » ');
    }

    public function getCanonicalAttribute()
    {

        $stack = collect([strtolower($this->name)]);
        $parent = $this->parent;
        while ($parent) {
            $stack->push(strtolower($parent->name));
            $parent = $parent->parent;
        }

        return $this->path = $stack->reverse()->implode('/');
    }

}