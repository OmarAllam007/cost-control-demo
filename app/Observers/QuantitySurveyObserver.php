<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 19/12/16
 * Time: 01:08 م
 */

namespace App\Observers;


use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Survey;


class QuantitySurveyObserver
{
    function creating(Survey $qs)
    {
        if (!$qs->boq_id) {
            $wbs_ids = $qs->wbsLevel->getParentIds();
            $boq_id = Boq::whereIn('wbs_id', $wbs_ids)->where('item_code', $qs->item_code)->value('id');
            $qs->boq_id = $boq_id;
        }

        if (!$qs->cost_account) {
//            $last_qs_in_boq = \DB::table('qty_surveys')
//                ->where('boq_id', $boq_id)
//                ->where('wbs_level_id', $qs->wbs_level_id)
//                ->max('cost_account');
//            $token = collect(explode(".{$qs->item_code}.", $last_qs_in_boq))->last();
            $qs->cost_account = $qs->wbsLevel->code . '.' . $qs->item_code . '.' . $qs->qs_code;
        }
    }

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