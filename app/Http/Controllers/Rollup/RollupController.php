<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
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

        $rollup = new BreakdownRollup($project, $request->get('cost_accounts', []));
        $status = $rollup->handle();

        return \Redirect::route('rollup.edit', $project);
    }

    function edit(Project $project)
    {
        $this->authorize('cost_owner', $project);

        return view('rollup.edit', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $rollup = new ImportantResourcesRollup($project, $request->get('cost_accounts', []));
        $status = $rollup->handle();

        return \Redirect::route('rollup.edit', $project);
    }
}
