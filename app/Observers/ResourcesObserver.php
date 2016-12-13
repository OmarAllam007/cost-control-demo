<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 12/12/16
 * Time: 01:38 م
 */

namespace App\Observers;


use App\BreakDownResourceShadow;
use App\Resources;
use App\Unit;

class ResourcesObserver
{

    function updated(Resources $resource)
    {
        $shadows = BreakDownResourceShadow::where('resource_id',$resource->id)->get();
        foreach ($shadows as $shadow){
            $shadow->resource_waste = $resource->waste;
            $shadow->unit_price = $resource->rate;
            $shadow->measure_unit = Unit::find($resource->unit)->type;
            $shadow->save();
        }

    }

}