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

class BreakdownObserver
{

    function updating(Breakdown $breakdown)
    {
        if ($breakdown->isDirty('cost-account')) {
            $breakdown->variables()->update(['qty_survey_id' => $breakdown->qty_survey->id]);
        }
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