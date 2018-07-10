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
use App\Jobs\CacheCsiCategoryTree;
use App\Productivity;

class ProductivityObserver
{
    function updating(Productivity $productivity)
    {
        $productivity->after_reduction = ($productivity->reduction_factor * $productivity->daily_output) + $productivity->daily_output;
    }

    function updated(Productivity $productivity)
    {
        BreakDownResourceShadow::where('productivity_id', $productivity->id)
            ->select(['id', 'breakdown_resource_id'])
            ->where('project_id', $productivity->project_id)->get()
            ->each(function (BreakDownResourceShadow $shadow) {
                $shadow->breakdown_resource->updateShadow();
            });
    }


}