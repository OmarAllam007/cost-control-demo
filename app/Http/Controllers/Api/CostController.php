<?php

namespace App\Http\Controllers\Api;

use App\CostResource;
use App\Http\Controllers\Controller;
use App\WbsLevel;
use App\WbsResource;

class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level)
    {
        return WbsResource::where('wbs_level_id', $wbs_level->id)->where('period_id', $wbs_level->project->open_period()->id)
            ->joinShadow()
            ->get();
    }
}
