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

    function updated(Breakdown $breakdown)
    {
        $resources = BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->get();
        /** @var BreakDownResourceShadow $shadow */
        foreach ($shadows as $shadow) {
            $shadow->wbs_id = $breakdown->wbs_level_id;
            $shadow->update();
        }
    }

    function deleted(Breakdown $breakdown){
        $breakdown->variables()->delete();
    }
}