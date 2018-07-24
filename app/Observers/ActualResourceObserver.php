<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 12/19/16
 * Time: 12:45 PM
 */

namespace App\Observers;


use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\StoreResource;
use App\WbsResource;

class ActualResourceObserver
{
    function saved(ActualResources $resource)
    {
//        $this->updateShadow($resource);
    }

    function deleted(ActualResources $resource)
    {
        $budget = BreakDownResourceShadow::where('breakdown_resource_id', $resource->breakdown_resource_id)
            ->first();

        $latest = ActualResources::where('breakdown_resource_id', $resource->breakdown_resource_id)
            ->where('period_id', '<', $resource->period_id)->latest()->first();

//        $this->updateShadow($resource);

        if ($latest) {
            $budget->update(['progress' => $latest->progress, 'status' => $latest->status]);
        } else {
            $budget->update(['progress' => 0, 'status' => 'Not Started']);
        }

        StoreResource::where('actual_resource_id', $resource->id)->delete();
    }

    protected function updateShadow(ActualResources $resource)
    {
        $conditions = [
            'breakdown_resource_id' => $resource->breakdown_resource_id,
            'period_id' => $resource->period_id,
        ];

        $budgetShadow = BreakDownResourceShadow::whereBreakdownResourceId($resource->breakdown_resource_id)->first();
        $budgetShadow->ignore_cost = true;
        $budgetShadow->appendFields();

        if ($budgetShadow->curr_qty) {
            $attributes = $budgetShadow->toArray();
            $attributes['period_id'] = $resource->period_id;
            CostShadow::updateOrCreate($conditions, $attributes);
        } else {
            CostShadow::where($conditions)->delete();
        }



        /*$trans = WbsResource::joinShadow()
            ->where('wbs_resources.breakdown_resource_id', $resource->breakdown_resource_id)
            ->where('period_id', $resource->period_id)
            ->first();


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
        }*/
    }
}