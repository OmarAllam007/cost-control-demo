<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\CostAccountRollup;
use Illuminate\Http\Request;

class CostAccountRollupController extends Controller
{
    function create(Project $project)
    {
        $this->authorize('cost_owner', $project);

        // We get WBS levels from WbsComposer
        return view('rollup.cost-account', compact('project'));
    }

    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $rollup = new CostAccountRollup($project,
            $request->get('cost_account', []),
            $request->only('budget_unit', 'measure_unit', 'to_date_qty', 'progress'));

        $status = $rollup->handle();

        $message = "$status Cost accounts have been rolled up";
        if ($request->wantsJson()) {
            return ['ok' => 'true', 'message' => $message];
        }

        flash($message, 'success');
        return redirect("/project/{$project->id}/rollup-semi-cost-account");
    }
}