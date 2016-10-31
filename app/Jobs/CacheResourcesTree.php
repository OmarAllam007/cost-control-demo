<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Resources;
use App\ResourceType;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheResourcesTree extends Job
{
    public function handle()
    {
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
                return ['id' => $resource->id, 'name' => $resource->name, 'json' => $resource->morphToJSON()];
            });
        }

        return $tree;
    }


}
