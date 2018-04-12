<?php

namespace App\Support;

use App\ActivityDivision;
use function collect;

class ActivityDivisionTree
{
    function __construct()
    {
        $this->divisions = ActivityDivision::with('activities')
            ->orderBy('code')->orderBy('name')
            ->get()
            ->groupBy('parent_id');
    }

    function get($parent = 0)
    {
        return $this->divisions->get($parent, collect())->map(function($division) {
            $division->subtree = $this->get($division->id);
            return $division;
        });
    }
}