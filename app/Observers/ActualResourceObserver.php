<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 12/19/16
 * Time: 12:45 PM
 */

namespace App\Observers;


use App\ActualResources;
use App\CostShadow;
use App\WbsResource;

class ActualResourceObserver
{
    function created(ActualResources $resource)
    {
        $attributes = WbsResource::joinShadow()->where('wbs_resources.breakdown_resource_id', $resource->breakdown_resource_id)
            ->where('wbs_resources.period_id', $resource->period_id)
            ->where('wbs_resources.resource_id', $resource->resource_id)
            ->first()->toArray();

        $attributes['batch_id'] = $resource->batch_id;

        $shadow = CostShadow::firstOrCreate([
            'breakdown_resource_id' => $resource->breakdown_resource_id,
            'period_id' => $resource->period_id,
            'resource_id' => $resource->resource_id,
        ]);

        $shadow->update($attributes);
    }
}