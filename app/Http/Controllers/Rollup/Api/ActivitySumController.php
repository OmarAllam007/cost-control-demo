<?php

namespace App\Http\Controllers\Rollup\Api;

use App\Http\Controllers\Controller;
use App\Rollup\Actions\SumActivity;
use App\WbsLevel;
use Illuminate\Http\Request;

class ActivitySumController extends Controller
{
    function store(WbsLevel $wbs, Request $request)
    {
        $this->authorize('cost_owner', $wbs->project);

        $handler = new SumActivity($wbs);
        $handler->handle();

        if ($request->wantsJson()) {
            return ['ok' => true];
        }

        return redirect()->route('project.cost-control', $wbs->project);
    }
}