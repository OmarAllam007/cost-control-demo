<?php

namespace App\Observers;

use App\BreakdownResource;
use App\CostResource;
use App\CostShadow;

class CostShadowObserver
{
    function deleted(CostShadow $resource)
    {
        $conditions = [
            'project_id' => $resource->project_id,
            'resource_id' => $resource->resource_id,
            'period_id' => $resource->period_id
        ];

        $figures = CostShadow::where($conditions)
            ->selectRaw('SUM(to_date_cost) as cost, AVG(to_date_qty) as qty')
            ->first()->toArray();

        $rate = 0;
        if ($figures['qty']) {
            $rate = $figures['cost'] / $figures['qty'];
        }
        CostResource::where($conditions)->update(compact('rate'));
    }
}