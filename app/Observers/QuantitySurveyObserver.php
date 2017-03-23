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
use App\Http\Controllers\Exports\QuantitySurvey;
use App\Survey;
use Illuminate\Database\Eloquent\Builder;

class QuantitySurveyObserver
{
    function created(Survey $qs)
    {
        $this->updateResources($qs);
    }


    function updated(Survey $qs)
    {
        $this->updateResources($qs);
    }

    function deleting(Survey $qs)
    {
        $ids = BreakDownResourceShadow::where('project_id', $qs->project_id)
            ->whereIn('wbs_id', $qs->wbsLevel->getChildrenIds())
            ->where('cost_account', $qs->cost_account)->pluck('breakdown_resource_id');

        BreakdownResource::whereIn('id', $ids)->get()->each(function (BreakdownResource $resource) {
            $resource->budget_qty = 0;
            $resource->eng_qty = 0;
            $resource->save();
        });
    }

    protected function updateResources(Survey $qs)
    {
        $ids = BreakDownResourceShadow::whereIn('wbs_id', $qs->wbsLevel->getChildrenIds())
            ->where('cost_account', $qs->cost_account)
            ->pluck('breakdown_resource_id');

        BreakdownResource::whereIn('id', $ids)->get()
            ->each(function (BreakdownResource $resource) use ($qs) {
            $resource->budget_qty = $qs->budget_qty;
            $resource->eng_qty = $qs->eng_qty;
            $resource->save();
        });
    }

}