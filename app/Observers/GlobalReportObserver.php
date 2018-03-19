<?php
namespace App\Observers;

use App\Jobs\CacheGlobalReportJob;

class GlobalReportObserver
{

    function saved($model)
    {
//        dispatch(new CacheGlobalReportJob());
    }

    function deleted($model)
    {
//        dispatch(new CacheGlobalReportJob());
    }
}