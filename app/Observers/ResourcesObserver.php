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
        if (!$resource->resource_code) {
            $resource->resource_code = $this->generateResourceCode($resource);
        }

        if ($resource->project_id && !$resource->resource_id) {
            $attributes = $resource->getAttributes();
            unset($attributes['project_id']);
            $original = Resources::create($attributes);

            $resource->resource_id = $original->id;
            $resource->resource_code = $original->resource_code;
        }
    }

    function updated(Resources $resource)
    {
        $resource->updateBreakdownResources();
//        dispatch(new CacheResourcesInQueue);
    }

    function deleted(Resources $resource)
    {
//        dispatch(new CacheResourcesInQueue);
    }

    public function generateResourceCode($resource)
    {
        $lastResourceInType = Resources::where('resource_type_id', $resource->resource_type_id)->max('resource_code');

        if ($lastResourceInType) {
            $tokens = explode('.', $lastResourceInType);
            $last = count($tokens) - 1;
            $length = strlen($tokens[$last]);
            $tokens[$last] = sprintf("%0{$length}d", $tokens[$last] + 1);
            return implode('.', $tokens);
        }

        return $resource->types->code . '.001';
    }

}