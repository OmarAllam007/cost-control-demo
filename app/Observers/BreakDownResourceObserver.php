<?php

namespace App\Observers;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;

class BreakDownResourceObserver
{
    public function created(BreakdownResource $resource)
    {
        BreakDownResourceShadow::create($resource->toArray());
    }

}