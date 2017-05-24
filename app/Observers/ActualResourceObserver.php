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
    function saved(ActualResources $resource)
    {
        $this->updateShadow($resource);
    }

    function deleted(ActualResources $resource)
    {
        $this->updateShadow($resource);
    }

    protected function updateShadow(ActualResources $resource)
    {
        $trans = WbsResource::joinShadow()
            ->where('wbs_resources.breakdown_resource_id', $resource->breakdown_resource_id)
            ->where('period_id', $resource->period_id)
            ->first();

        $conditions = [
            'breakdown_resource_id' => $resource->breakdown_resource_id,
            'period_id' => $resource->period_id,
        ];

        if ($trans) {
            $attributes = $trans->toArray();

            $shadow = CostShadow::firstOrCreate($conditions);
            $shadow->fill(collect($attributes)->only($shadow->getFillable())->toArray());
            $shadow->manual_edit = false;
            $shadow->save();
        } else {
            CostShadow::where($conditions)->get()->each(function ($resource) {
                $resource->delete();
            });
        }
    }
}