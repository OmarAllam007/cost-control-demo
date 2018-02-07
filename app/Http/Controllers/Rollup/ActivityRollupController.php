<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Rollup\Actions\ActivityRollup;
use Illuminate\Http\Request;
use App\Project;

class ActivityRollupController extends Controller
{
    function store(Project $project, Request $request)
    {
        $this->authorize('actual_resources', $project);

        $codes = $project->shadows()->selectRaw('distinct code')->pluck('code');

        $result = (new ActivityRollup($project, $codes))->handle();

        flash("$result activities have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }

    function update(Project $project, $code, Request $request)
    {
        $this->authorize('actual_resources', $project);

        (new ActivityRollup($project, [$code]))->handle();

        return \Redirect::route('project.cost-control', $project);
    }
}