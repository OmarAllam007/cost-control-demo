<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Rollup\Actions\ActivityRollup;
use function flash;
use Illuminate\Http\Request;
use App\Project;

class ActivityRollupController extends Controller
{
    // Rollup whole project
    function store(Project $project)
    {
        $this->authorize('cost_owner', $project);
        
        if (!$project->open_period()) {
            flash('Cannot rollup only when there is open period');
            return back();
        }

        $codes = $project->shadows()->selectRaw('distinct code')->pluck('code');

        $result = (new ActivityRollup($project, $codes))->handle();

        flash("$result activities have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }

    // Specify activity
    function update(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        if (!$project->open_period()) {
            flash('Rollup can only be done when there is open period');
            return back();
        }

        $count = (new ActivityRollup($project, $request->input('codes', [])))->handle();

        if ($request->wantsJson()) {
            return ['ok' => true, 'count' => $count];
        }

        flash("$count activities have been rolled up", 'success');
        return redirect()->route('project.cost-control', $project);
    }
}