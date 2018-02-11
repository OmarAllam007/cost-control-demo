<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\ImportantResourcesRollup;
use Illuminate\Http\Request;

class ImportantResourcesRollupController extends Controller
{
    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $data = $project->breakdowns()->has('rollable_resources')
            ->with('rollable_resources')->get()->keyBy('id')->map(function ($breakdown) {
                return $breakdown->rollable_resources->pluck('id', 'id');
            });

        $rollup = new ImportantResourcesRollup($project, $data);
        $rollup->handle();


        flash("Project has been rolled up on cost account level except important resources", 'success');
        return redirect()->route('project.cost-control', $project);
    }
}