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
use App\Project;
use App\Resources;
use App\Unit;

class ResourcesObserver
{
    function created(Resources $resource){
        $cache = new ResourcesCache();
        $cache->cacheResources();
    }

    function updated(Resources $resource)
    {

        $shadows = BreakDownResourceShadow::where('resource_name', $resource->name)
            ->orWhere('resource_code', $resource->resource_code)
            ->where('project_id', $resource->project_id)
            ->get();

        foreach ($shadows as $shadow) {
            $shadow->resource_name = $resource->name;
            $shadow->resource_waste = $resource->waste;
            $shadow->unit_price = $resource->rate;
            $shadow->measure_unit = Unit::find($resource->unit)->type;
            $shadow->budget_cost = $shadow->budget_unit * $shadow->unit_price;
            $shadow->boq_equivilant_rate = ($shadow->budget_cost / $shadow->eng_qty)??0;
            $shadow->save();
        }
        $cache = new ResourcesCache();
        $cache->cacheResources();

    }
    function saving(Resources $resource){
        $cache = new ResourcesCache();
        $cache->cacheResources();
    }

    function deleted(Resources $resource){
        $cache = new ResourcesCache();
        $cache->cacheResources();
    }

}