<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 19/12/16
 * Time: 01:08 م
 */

namespace App\Observers;


use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Survey;
use Illuminate\Database\Eloquent\Builder;

class QuantitySurveyObserver
{

    function updated(Survey $quantitySurvey)
    {
        $ids = BreakDownResourceShadow::whereIn('wbs_id', $quantitySurvey->wbsLevel->getChildrenIds())
            ->where('cost_account', $quantitySurvey->cost_account)
            ->pluck('breakdown_resource_id');

        $resources = BreakdownResource::whereIn('id', $ids)->get();

        foreach ($resources as $resource) {
            $resource->budget_qty = $quantitySurvey->budget_qty;
            $resource->eng_qty = $quantitySurvey->eng_qty;
            $resource->save();
        }
    }

    function deleting(Survey $survey)
    {
        $ids = BreakDownResourceShadow::where('project_id', $survey->project_id)
            ->whereIn('wbs_id', $survey->wbsLevel->getChildrenIds())
            ->where('cost_account', $survey->cost_account)->pluck('breakdown_resource_id');

        $resources = BreakdownResource::whereIn('id', $ids)->get();

        foreach ($resources as $resource) {
            $resource->budget_qty = 0;
            $resource->eng_qty = 0;
            $resource->save();
        }
    }

}