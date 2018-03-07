<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Rollup\Actions\SumCostAccount;
use App\WbsLevel;

class CostAccountSumController extends Controller
{
    function store(WbsLevel $wbs)
    {
        $this->authorize('cost_owner', $wbs->project);

        $handler = new SumCostAccount($wbs);
        $handler->handle();

        return redirect()->route('project.cost-control', $wbs->project);
    }
}