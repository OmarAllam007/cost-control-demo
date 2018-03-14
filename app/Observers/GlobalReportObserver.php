<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 20/12/17
 * Time: 4:47 PM
 */

namespace App\Observers;


use App\Jobs\CacheGlobalReportJob;

class GlobalReportObserver
{

    function saved($model)
    {
        dispatch(new CacheGlobalReportJob());
    }

    function deleted($model)
    {
        dispatch(new CacheGlobalReportJob());
    }
}