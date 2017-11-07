<?php

namespace App\Support;

use App\ResourceType;
use Illuminate\Support\Collection;

class ResourceTypesTree
{
    /** @var Collection */
    private $resource_types;

    function get()
    {
        $this->resource_types = ResourceType::where('archived', 0)
            ->get()
            ->load('db_resources')
            ->groupBy('parent_id');

        return $this->tree();
    }

    private function tree($parent = 0)
    {
        return $this->resource_types->get($parent, collect())->map(function ($type) {
            $type->subtree = $this->tree($type->id);
            return $type;
        });
    }
}