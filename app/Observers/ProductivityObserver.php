<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 12/12/16
 * Time: 01:38 م
 */

namespace App\Observers;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Productivity;

class ProductivityObserver
{

    function updated(Productivity $productivity)
    {
//        $brResources = BreakdownResource::where('productivity_id', $productivity->productivity_id)->pluck('id')->toArray();
        $shadows = BreakDownResourceShadow::where('productivity_ref', $productivity->csi_code)->where('project_id', $productivity->project_id)->get();
        foreach ($shadows as $shadow) {
            $shadow->productivity_ref = $productivity->csi_code;
            $shadow->productivity_output = $productivity->after_reduction;
            $shadow->save();
        }

    }


}