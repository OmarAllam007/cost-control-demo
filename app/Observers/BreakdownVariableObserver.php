<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 01/01/17
 * Time: 06:54 م
 */

namespace App\Observers;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownVariable;
use App\Formatters\BreakdownResourceFormatter;
use App\Resources;

class BreakdownVariableObserver
{
    function updated(BreakdownVariable $var)
    {
        BreakdownResource::where('breakdown_id', $this->breakdown_id)->get()->each(function(BreakdownResource $resource){
            $resource->updateShadow();
        });
    }
}