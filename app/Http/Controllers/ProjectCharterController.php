<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectCharterController extends Controller
{
    function edit(Project $project)
    {
        $this->authorize('budget_owner', $project);

        return view('project.charter-data', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('budget_owner', $project);

        $fields = ['description', 'assumptions', 'discipline_brief',
            'project_type', 'contract_type', 'consultant',
            'dry_cost', 'overhead_and_gr', 'estimated_profit_and_risk',
            'project_contract_signed_value'
        ];

        foreach ($fields as $field) {
            $project[$field] = $request->input($field);
        }
        $project->save();

        flash('Project charter has been updated', 'success');
        return \Redirect::route('project.budget', $project);
    }

    /*protected function authorize($project)
    {
        if (cannot('budget_owner', $project)) {
            flash('You are not authorized to do this action');
            abort(301, 'You are not authorized to do this action', [
                'location' => route('project.budget', $project)
            ]);
        }
    }*/
}
