<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\BreakdownRollup;

class CostAccountRollupController extends Controller
{
    function store(Project $project)
    {
        $this->authorize('cost_owner', $project);

        $project->breakdowns()
            ->select('id')->chunk(50, function ($breakdowns) use ($project) {
                \DB::beginTransaction();
                $rollup = new BreakdownRollup($project, $breakdowns->pluck('id')->toArray());
                $rollup->handle();
                \DB::commit();
            });


        flash("Project has been rolled up on cost account level", 'success');
        return redirect()->route('project.cost-control', $project);
    }
}