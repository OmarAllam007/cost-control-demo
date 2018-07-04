<?php

namespace App\Http\Controllers\Api;

use App\Support\ActivityLog;
use App\WbsLevel;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    function show(WbsLevel $wbs)
    {
        $this->authorize('actual_resources', $wbs->project);

        $code = request('code');
        $activityLog = new ActivityLog($wbs, $code);

        return $activityLog->handle();
    }
}
