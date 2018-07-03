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

        $project->is_activity_rollup = 1;
        $project->save();

        flash("$result activities have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }

    // Specify activity
    function update(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        if (!$project->open_period()) {
            $msg = 'Rollup can only be done when there is open period';
            if (!$request->wantsJson()) {
                return ['ok' => false, 'message' => $msg];
            }

            flash($msg);
            return back();
        }

        $codes = collect($request->input('codes', []));
        $count = (new ActivityRollup($project, $codes))->handle();

        $msg = "$count activities have been rolled up";
        if ($request->wantsJson()) {
            return ['ok' => true, 'message' => $msg];
        }
        flash($msg, 'success');
        return redirect()->route('project.cost-control', $project);
    }
}