<?php

namespace App\Support;

use App\ResourceType;
use Illuminate\Support\Collection;

class ResourceTypesTree
{
    /** @var Collection */
    private $resource_types;

    private $current_type;

    private $include_resources = true;

    /** @return Collection */
    function get()
    {
        $resource_types = ResourceType::where('archived', 0)->orderBy('name')->get();

        if ($this->include_resources) {
            $resource_types = $resource_types->load('db_resources');
        }

        $this->resource_types = $resource_types->groupBy('parent_id');

        return $this->tree()->keyBy('id');
    }

    private function tree($parent = 0)
    {
        return $this->resource_types->get($parent, collect())->map(function ($type) use ($parent) {
            if ($parent == 0) {
                $this->current_type = $type;
            }

            $type->subtree = $this->tree($type->id);

            if ($this->include_resources) {
                $type->db_resources->map(function($resource) {
                    $resource->root_type = $this->current_type->name;
                    return $resource;
                });
            }

            return $type;
        });
    }

    function setIncludeResources($value = true)
    {
        $this->include_resources = $value;
        return $this;
    }
}