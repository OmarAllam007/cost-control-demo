<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 10:48 ص
 */

namespace App\Observers;


use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownVariable;
use App\Survey;
use App\WbsLevel;

class BreakdownObserver
{

    function creating($breakdown)
    {
        if (!$breakdown->qs_id) {
            /** @var WbsLevel $wbs */
            $wbs = WbsLevel::find($breakdown->wbs_level_id);
            $qty_survey = Survey::where('cost_account', $breakdown->cost_account)
                ->whereIn('wbs_level_id', $wbs->getParentIds())
                ->orderBy('wbs_level_id', 'DESC')
                ->first();

            if ($qty_survey) {
                $breakdown->qs_id = $qty_survey->id;
                $breakdown->boq_id = $qty_survey->boq_id;
            }
        }

        $breakdown->sap_code = $breakdown->wbs_level->sap_code . '.' . $breakdown->std_activity->sap_code_partial;
    }


    function updating(Breakdown $breakdown)
    {
        if ($breakdown->isDirty('cost-account')) {
            $breakdown->variables()->update(['qty_survey_id' => $breakdown->qty_survey->id]);
        }

        $breakdown->sap_code = $breakdown->wbs_level->sap_code . '.' . $breakdown->std_activity->sap_code_partial;
    }

    function updated(Breakdown $breakdown)
    {
        $shadows = BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->get();
        /** @var BreakDownResourceShadow $shadow */
        $shadows->each(function (BreakDownResourceShadow $shadow){
            $shadow->breakdown_resource->updateShadow();
        });
    }

    function deleted(Breakdown $breakdown){
        $breakdown->variables()->delete();
    }
}