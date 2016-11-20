<?php

namespace App\Observers;

use App\BreakdownResource;
use App\BreakDownResourceShadow;

class BreakDownResourceObserver
{
    public function created(BreakdownResource $resource)
    {
        dd($resource->toArray());
    }

}