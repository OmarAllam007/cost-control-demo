<?php

namespace App\Http\Controllers;

use App\CostConcern;
use App\Project;
use Illuminate\Http\Request;

class CostConcernsController extends Controller
{

    function create(Project $project)
    {

    }

    function store(Project $project, Request $request)
    {
        $this->authorize('reports', $project);

        $attributes = $request->only('report_name', 'data', 'comment');
        $attributes['project_id'] = $project->id;
        $attributes['period_id'] = $project->open_period()->id;

        CostConcern::create($attributes);

        if ($request->wantsJson()) {
            return ['ok' => 'true'];
        }

        flash('Issue has been saved', 'info');
        return back();
    }

    function destroy(CostConcern $concern, Request $request)
    {
        $this->authorize('cost_owner', $concern->project);

        $concern->delete();

        if ($request->wantsJson()) {
            return ['ok' => 'true'];
        }

        flash('Issue has been deleted', 'info');
        return back();
    }
}