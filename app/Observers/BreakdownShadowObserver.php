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
    function creating($resource)
    {
        // Check if there is activity rollup for this resource
        $rollupResource = BreakDownResourceShadow::where('project_id', $resource->project_id)
            ->where('code', $resource->code)->where('is_rollup', 1)->whereRaw('code = resource_code')->first();

        // if no activity rollup check for cost account rollup
        if (!$rollupResource) {
            $rollupResource = BreakDownResourceShadow::where('project_id', $resource->project_id)
                ->where('cost_account', $resource->cost_account)->where('is_rollup', 1)->whereRaw('code = cost_account')->first();
        }

        // Other rollup types can/should be done manually as we cannot decide if it should be included here.

        // Update the required fields accordingly if we found the resource
        if ($rollupResource) {
            $resource->rolled_up_at = now()->format('Y-m-d H:i:s');
            $resource->rollup_resource_id = $rollupResource->id;
            $resource->show_in_cost = 0;
        }
    }

    function created($resource)
    {
        // We update breakdown resource here as in creating it shall create an infinite loop
        if ($resource->rollup_resource_id) {
            $rollupResource = BreakDownResourceShadow::find($resource->rollup_resource_id);
            $resource->breakdown_resource->update(['rollup_resource_id' => $rollupResource->breakdown_resource_id, 'rolled_up_at' => $resource->rolled_up_at]);
            $this->updateRollup($resource);
        }
    }

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

        if ($resource->rollup_resource_id || $resource->sum_resource_id) {
            $this->updateRollup($resource);
        }
    }

    private function updateRollup(BreakdownResourceShadow $resource)
    {
        //todo: needs further discussion on how to update Qty
        $rollup_resource = BreakDownResourceShadow::find($resource->rollup_resource_id);
        $budget_cost = BreakDownResourceShadow::where('rollup_resource_id', $resource->rollup_resource_id)->sum('budget_cost');
        $unit_price = 0;

        if ($rollup_resource->budget_unit) {
            $unit_price = $budget_cost / $rollup_resource->budget_unit;
        }

        $important = BreakDownResourceShadow::where('rollup_resource_id', $resource->rollup_resource_id)->where('important', 1)->exists();

        if ($resource->rollup_resource_id) {
            BreakDownResourceShadow::where('id', $resource->rollup_resource_id)->update(compact('budget_cost', 'unit_price', 'important'));
        } elseif ($resource->sum_resource_id) {
            $budget_unit = BreakDownResourceShadow::where('rollup_resource_id', $resource->rollup_resource_id)->sum('budget_unit');
            BreakDownResourceShadow::where('id', $resource->sum_resource_id)->update(compact('budget_cost', 'unit_price', 'important', 'budget_unit'));
        }
    }


}