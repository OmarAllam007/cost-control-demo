<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 11:04 ص
 */

namespace App\Observers;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\WbsResource;

class BreakdownShadowObserver
{
    function updating(BreakDownResourceShadow $resource)
    {
        $dirty = $resource->getDirty();
        if (isset($dirty['progress']) || isset($dirty['status'])) {
            $resource->update_cost = true;
        }

        $resource->update_cost = false;
    }

    function updated(BreakDownResourceShadow $resource)
    {
        if ($resource->update_cost) {
            $conditions = [
                'wbs_resources.breakdown_resource_id' => $resource->breakdown_resource_id,
                'wbs_resources.project_id' => $resource->project_id
            ];

            $recalculated = WbsResource::joinShadow()->where($conditions)->first();

            $conditions = [
                'period_id' => $resource->project->open_period()->id,
                'breakdown_resource_id' => $resource->breakdown_resource_id,
                'project_id' => $resource->project_id
            ];

            $costShadow = CostShadow::where($conditions)->first();
            if ($costShadow && $recalculated) {
                $costShadow->update($recalculated->toArray());
            }
        }
    }
}