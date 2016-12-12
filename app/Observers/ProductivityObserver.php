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

        $resources = BreakDownResourceShadow::where('productivity_id', $productivity->productivity_id)->get();
        if ($resources) {

        }
    }


}