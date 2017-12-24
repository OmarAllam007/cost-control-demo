<?php

namespace App\Support;

use App\ResourceType;
use Illuminate\Support\Collection;

class ResourceTypesTree
{
    /** @var Collection */
    private $resource_types;

    private $current_type;

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
        return $this->resource_types->get($parent, collect())->map(function ($type) use ($parent) {
            if ($parent == 0) {
                $this->current_type = $type;
            }

            $type->subtree = $this->tree($type->id);
            $type->db_resources->map(function($resource) {
                $resource->root_type = $this->current_type->name;
                return $resource;
            });
            return $type;
        });
    }
}