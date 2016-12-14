<?php

namespace App\Http\Controllers\Api;

use App\CostResource;
use App\Http\Controllers\Controller;
use App\WbsLevel;

class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level)
    {
        return collect();
    }
}
