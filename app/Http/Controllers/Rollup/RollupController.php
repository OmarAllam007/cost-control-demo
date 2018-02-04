<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\BreakdownRollup;
use App\Rollup\Actions\ImportantResourcesRollup;
use Illuminate\Http\Request;

class RollupController extends Controller
{
    function create(Project $project)
    {
        $this->authorize('cost_owner', $project);

        // We get WBS levels from WbsComposer
        return view('rollup.create', compact('project'));
    }

    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $rollup = new BreakdownRollup($project, $request->get('cost_account', []));
        $status = $rollup->handle();

        flash("$status Cost accounts have been rolled up", 'success');
        return \Redirect::route('project.rollup.edit', $project);
    }

    function edit(Project $project)
    {
        $this->authorize('cost_owner', $project);

        return view('rollup.edit', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $rollup = new ImportantResourcesRollup($project, $request->get('resources', []));
        $status = $rollup->handle();

        flash("{$status['resources']} Resources in {$status['cost_accounts']} cost accounts have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }
}
