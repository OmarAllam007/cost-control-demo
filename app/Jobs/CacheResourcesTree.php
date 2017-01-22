<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Resources;
use App\ResourceType;
use Illuminate\Support\Collection;


class CacheResourcesTree extends Job
{

    /** @var Collection */
    protected $types;

    public function handle()
    {
//        set_time_limit(60);
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
            $tree['resources'] = $type->resources->map(function(Resources $resource) {
                 $attributes = ['id' => $resource->id,'code'=>$resource->resource_code, 'name' => $resource->name,'project_id'=>$resource->project_id];
                 if ($resource->project && $resource->project_id) {
                     $attributes['project_name'] = $resource->project->name;
                 }

                 if ($resource->types) {
                     $attributes['root_type'] = $resource->types->root->name;
                 } else{
                     $attributes['root_type'] = '';
                 }
                 return $attributes;
            });
        }

        return $tree;
    }


}
