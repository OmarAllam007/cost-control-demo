<?php

namespace App\Http\Controllers;

use App\Project;
use App\ResourceCode;

class ResourceCodeController extends Controller
{
    function delete(Project $project)
    {
        if (cannot('resource_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        ResourceCode::where('project_id', $project->id)->delete();

        flash('All resource mapping has been delete for this project', 'info');
        return \Redirect::route('resources.import-codes', compact('project'));
    }
}
