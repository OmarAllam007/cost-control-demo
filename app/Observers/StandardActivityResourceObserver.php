<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 10:09 ص
 */

namespace App\Observers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\StdActivityResource;
use Illuminate\Database\Eloquent\Builder;

class StandardActivityResourceObserver
{
    function updated(StdActivityResource $resource)
    {
        BreakdownResource::where('std_activity_resource_id', $resource->id)->get()->each(function (BreakdownResource $resource) {
            $resource->updateShadow();
        });
    }
}