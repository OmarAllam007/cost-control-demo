<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Resources;
use App\ResourceType;


class CacheResourcesTree extends Job
{
    public function handle()
    {
        set_time_limit(60);

        $tree = [];
        $types = ResourceType::tree()->get();

        foreach ($types as $type) {
            $treeType = $this->buildTypeTree($type);
            $tree[] = $treeType;
        }

        return $tree;
    }

    protected function buildTypeTree(ResourceType $type)
    {
        $tree = ['id' => $type->id, 'name' => $type->name, 'children' => [], 'resources' => []];
        if ($type->children->count()) {
            $tree['children'] = $type->children->map(function(ResourceType $child){
                return $this->buildTypeTree($child);
            });
        }

        if ($type->resources->count()) {
            $tree['resources'] = $type->resources()->whereNull('project_id')->get()->map(function(Resources $resource) {
                    return ['id' => $resource->id,'code'=>$resource->resource_code, 'name' => $resource->name,'project_id'=>$resource->project_id, 'json' => $resource->morphToJSON()];
            });
        }


        return $tree;
    }


}
