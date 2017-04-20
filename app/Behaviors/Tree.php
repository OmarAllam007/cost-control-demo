<?php

namespace App\Behaviors;

use App\Survey;
use Illuminate\Database\Eloquent\Builder;

trait Tree
{
    protected $tree_path;

    protected $children_ids;

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children()
    {
        $relation = $this->hasMany(static::class, 'parent_id');
        if (isset($this->orderBy)) {
            foreach ($this->orderBy as $order) {
                $relation->orderBy($order);
            }
        }
        return $relation;
    }


    public function scopeTree(Builder $query)
    {
        $query->parents()
            ->with('children')
            ->with('children.children')
            ->with('children.children.children');

        if (isset($this->orderBy)) {
            foreach ($this->orderBy as $order) {
                $query->orderBy($order);
            }
        }
    }

    public function scopeParents(Builder $query)
    {
        $query->where('parent_id', 0);
    }


    public function getPathAttribute()
    {
        if ($this->tree_path) {
            return $this->tree_path;
        }

        $stack = collect([$this->name]);
        $parent = $this->parent;
        while ($parent) {
            $stack->push($parent->name);
            $parent = $parent->parent;
        }

        return $this->tree_path = $stack->reverse()->implode(' » ');
    }

    public function getCanonicalAttribute()
    {

        $stack = collect([strtolower($this->name)]);
        $parent = $this->parent;
        while ($parent) {
            $stack->push(strtolower($parent->name));
            $parent = $parent->parent;
        }

        return $this->tree_path = $stack->reverse()->implode('/');
    }

    function getChildrenIds()
    {
        if (count($this->children_ids)) {
            return $this->children_ids;
        }

        $ids = collect($this->id);


        foreach ($this->children as $child) {
            $subids = $child->getChildrenIds();
            $ids = $ids->merge($subids);
        }

        return $this->children_ids = $ids;
    }

    function getPathArrayAttribute()
    {
        $stack = collect([$this->name]);
        $parent = $this->parent;
        while ($parent) {
            $stack->push($parent->name);
            $parent = $parent->parent;
        }

        return $stack->reverse();
    }
}