<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 12/12/16
 * Time: 01:38 م
 */

namespace App\Observers;


use App\BreakDownResourceShadow;
use App\Productivity;

class ProductivityObserver
{

    function updated(Productivity $productivity)
    {
        if ($productivity->project_id) {
            if ($productivity->productivity_id) {
                $resources = BreakDownResourceShadow::where('project_id', $productivity->project_id)->where('productivity_id', $productivity->productivity_id)->get();
            } else {
                $resources = BreakDownResourceShadow::where('project_id', $productivity->project_id)->where('productivity_id', $productivity->id)->get();
            }
            $output = $productivity->versionFor($productivity->project_id)->after_reduction;

            foreach ($resources as $resource) {
                $resource->productivity_output = $output;
                $resource->save();
            }
        }
    }
}