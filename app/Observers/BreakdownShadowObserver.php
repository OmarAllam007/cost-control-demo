<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 11:04 ص
 */

namespace App\Observers;


use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\WbsResource;
use function compact;

class BreakdownShadowObserver
{
    function updating(BreakDownResourceShadow $resource)
    {
        $resource->update_cost = $resource->project->open_period() &&
            $resource->isDirty(['progress', 'status', 'budget_unit', 'budget_cost']);
    }

    function updated(BreakDownResourceShadow $resource)
    {
        if ($resource->update_cost && $resource->project->open_period()) {
            $conditions = [
                'period_id' => $resource->project->open_period()->id,
                'breakdown_resource_id' => $resource->breakdown_resource_id,
                'project_id' => $resource->project_id
            ];

            CostShadow::firstOrCreate($conditions)->recalculate();

            $latestResource = ActualResources::where('breakdown_resource_id', $resource->breakdown_resource_id)
                ->latest()->first();

            if ($latestResource) {
                $latestResource->update(['progress' => $resource->progress, 'status' => $resource->status]);
            }
        }

        if ($resource->rollup_resource_id) {
            $this->updateRollup($resource);
        }
    }

    private function updateRollup(BreakdownResourceShadow $resource)
    {
        //todo: needs further discussion on how to update Qty
        $rollup_resource = BreakDownResourceShadow::find($resource->rollup_resource_id);
        $budget_cost = BreakDownResourceShadow::where('rollup_resource_id', $resource->rollup_resource_id)->sum('budget_cost');
        $unit_price = 0;
        if ($rollup_resource->qty) {
            $unit_price = $budget_cost / $rollup_resource->qty;
        }
        $important = BreakDownResourceShadow::where('rollup_resource_id', $resource->rollup_resource_id)->where('important', 1)->exists();

        BreakDownResourceShadow::where('id', $resource->rollup_resource_id)->update(compact('budget_cost', 'unit_price', 'important'));
    }


}