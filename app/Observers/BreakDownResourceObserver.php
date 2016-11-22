<?php

namespace App\Observers;

use App\BreakdownResource;

use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;

class BreakDownResourceObserver
{
    function created(BreakdownResource $resource)
    {
        $formatter = new BreakdownResourceFormatter($resource);
        BreakDownResourceShadow::create($formatter->toArray());
    }

    function updated(BreakdownResource $resource)
    {
        $formatter = new BreakdownResourceFormatter($resource);
        BreakDownResourceShadow::where('breakdown_resource_id', $resource->id)->update($formatter->toArray());
    }

    function deleted(BreakdownResource $resource)
    {
        BreakDownResourceShadow::where('breakdown_resource_id', $resource->id)->delete();
    }
}