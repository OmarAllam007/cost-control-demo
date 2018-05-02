<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 12/12/16
 * Time: 01:38 م
 */

namespace App\Observers;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Jobs\CacheResourcesInQueue;
use App\Project;
use App\Resources;
use App\Unit;

class ResourcesObserver
{
    function created(Resources $resource)
    {
//        dispatch(new CacheResourcesInQueue);
    }

    function creating(Resources $resource)
    {
        $resource->resource_code = $this->generateResourceCode($resource);

        if ($resource->project_id && !$resource->resource_id) {
            $attributes = $resource->getAttributes();
            unset($attributes['project_id']);
            $original = Resources::create($attributes);

            $resource->resource_id = $original->id;
            $resource->resource_code = $original->resource_code;
        }
    }

    function updating(Resources $resource)
    {
        if (!$resource->project_id && $resource->isDirty('resource_type_id')) {
            $resource->resource_code = $this->generateResourceCode($resource);
        }
    }

    function updated(Resources $resource)
    {
        if($resource->project_id) {
            $resource->updateBreakdownResources();
        } else {
            $this->updateResources($resource);
        }
    }

    function deleted(Resources $resource)
    {
//        dispatch(new CacheResourcesInQueue);
    }

    public function generateResourceCode($resource)
    {
        $lastResourceInType = Resources::where('resource_type_id', $resource->resource_type_id)
            ->whereNull('project_id')->whereNull('resource_id')
            ->orderByRaw('LENGTH(resource_code) DESC')
            ->orderBy('resource_code', 'DESC')
            ->value('resource_code');

        if ($lastResourceInType) {
            $tokens = explode('.', $lastResourceInType);
            $last = count($tokens) - 1;
            $length = strlen($tokens[$last]);
            $tokens[$last] = sprintf("%0{$length}d", $tokens[$last] + 1);
            return implode('.', $tokens);
        }

        return $resource->types->code . '.001';
    }

    /**
     * @param Resources $resource
     */
    private function updateResources(Resources $resource)
    {
        $project_ids = Project::pluck('id');

        $resource_ids = \DB::table('resources')->where('resource_id', $resource->id)
            ->whereIn('project_id', $project_ids)->pluck('id');

        \DB::table('resources')->whereIn('id', $resource_ids)
            ->update([
                'name' => $resource->name,
                'resource_type_id' => $resource->resource_type_id,
                'resource_code' => $resource->resource_code]);

        $root_type = $resource->types->root->name;
        $root_id = $resource->types->root->id;

        \DB::table('break_down_resource_shadows')->whereIn('resource_id', $resource_ids)
            ->update([
                'resource_name' => $resource->name,
                'resource_code' => $resource->resource_code,
                'resource_type_id' => $root_id,
                'resource_type' => $root_type
            ]);
    }

}