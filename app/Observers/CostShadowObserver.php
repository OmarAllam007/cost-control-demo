<?php

namespace App\Observers;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostResource;
use App\CostShadow;
use App\Jobs\UpdateResourceDictJob;
use App\Support\CostShadowCalculator;

class CostShadowObserver
{
    function deleted(CostShadow $resource)
    {
//        $conditions = [
//            'project_id' => $resource->project_id,
//            'resource_id' => $resource->resource_id,
//            'period_id' => $resource->period_id
//        ];
//
//        $figures = CostShadow::where($conditions)
//            ->selectRaw('SUM(to_date_cost) as cost, SUM(to_date_qty) as qty')
//            ->first()->toArray();
//
//        $rate = 0;
//        if ($figures['qty']) {
//            $rate = $figures['cost'] / $figures['qty'];
//        }
//        CostResource::where($conditions)->update(compact('rate'));
        dispatch(new UpdateResourceDictJob($resource->project, collect([$resource->resource_id])));

        BreakDownResourceShadow::flushEventListeners();

        $latestShadow = CostShadow::where('breakdown_resource_id', $resource->breakdown_resource_id)
            ->where('period_id', '<', $resource->period_id)->first();

//        if ($latestShadow) {
//            $resource->budget->update(['progress' => $latestShadow->progress, 'status' => $latestShadow->status]);
//        } else {
//            $resource->budget->update(['progress' => 0, 'status' => 'Not Started']);
//        }
    }
}