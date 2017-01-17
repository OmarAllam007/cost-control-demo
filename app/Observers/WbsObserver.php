<?php

namespace App\Observers;

use App\Jobs\CacheWBSTreeInQueue;
use App\WbsLevel;

class WbsObserver
{
    function created(WbsLevel $wbs)
    {
        dispatch(new CacheWBSTreeInQueue($wbs->project));
    }

    function updated(WbsLevel $wbs)
    {
        dispatch(new CacheWBSTreeInQueue($wbs->project));
    }
}