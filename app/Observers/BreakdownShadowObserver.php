<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 11:04 ص
 */

namespace App\Observers;


use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\WbsResource;

class BreakdownShadowObserver
{
    function updating(BreakDownResourceShadow $resource)
    {
        $resource->update_cost = $resource->isDirty(['progress', 'status', 'budget_unit', 'budget_cost']);
    }

    function updated(BreakDownResourceShadow $resource)
    {
        if ($resource->update_cost) {
            $conditions = [
                'period_id' => $resource->project->open_period()->id,
                'breakdown_resource_id' => $resource->breakdown_resource_id,
                'project_id' => $resource->project_id
            ];

            $costShadow = CostShadow::firstOrCreate($conditions);
            $costShadow->progress = $resource->progress;
            $costShadow->status = $resource->status;
            $costShadow->recalculate(true);

            $latestResource = ActualResources::where('breakdown_resource_id', $resource->breakdown_resource_id)
                ->latest()->first();
            
            if ($latestResource) {
                $latestResource->update(['progress' => $resource->progress, 'status' => $resource->status]);
            }
        }
    }
}