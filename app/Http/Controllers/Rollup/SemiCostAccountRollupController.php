<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\SemiCostAccountRollup;
use Illuminate\Http\Request;

class SemiCostAccountRollupController extends Controller
{
    function create(Project $project)
    {
        $this->authorize('cost_owner', $project);

        return view('rollup.semi-cost-account', compact('project'));
    }

    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $rollup = new SemiCostAccountRollup($project,
            $request->get('resources', []),
            $request->only('budget_unit', 'measure_unit', 'to_date_qty', 'progress'));

        $status = $rollup->handle();

        flash("{$status['resources']} Resources in {$status['cost_accounts']} cost accounts have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }
}