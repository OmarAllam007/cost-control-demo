<?php

namespace App\Jobs;

use App\Resources;
use App\ResourceType;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class CacheResourcesTree extends Job
{

    /** @var Collection */
    protected $types;

    /** @var Collection */
    protected $resources;

    public function handle()
    {
        return \Cache::remember('resources-tree', Carbon::parse('+1 week'), function() {
            $this->types = ResourceType::where('archived', 0)->get()->groupBy('parent_id');
            $this->resources = Resources::with('project')->get()->groupBy('resource_type_id');

            return $this->buildTypeTree();
        });


    }

    protected function buildTypeTree($parent_id = 0)
    {
        $tree = $this->types->get($parent_id) ?: collect();

        return $tree->map(function (ResourceType $type) {
            $type->children = $this->buildTypeTree($type->id);

            $type->resources = ($this->resources->get($type->id) ?: collect())->map(function($resource) {
                if ($resource->project_id) {
                    $resource->project_name = $resource->project->name;
                }

                return $resource->getAttributes();
            });

            return $type->getAttributes();
        });
    }


}
