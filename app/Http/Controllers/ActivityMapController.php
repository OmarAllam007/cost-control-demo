<?php

namespace App\Http\Controllers;

use App\ActivityMap;
use App\Jobs\ImportActivityMapsJob;
use App\Project;
use Illuminate\Http\Request;

class ActivityMapController extends Controller
{
    function import(Project $project)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        return view('activity-map.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $count = $this->dispatch(new ImportActivityMapsJob($project, $file->path()));

        flash($count . ' Records have been imported', 'success');
        return \Redirect::route('project.cost-control', $project);
    }

    function delete(Project $project)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        ActivityMap::where('project_id', $project->id)->delete();

        flash('All activity mapping for this project has been deleted', 'info');
        return \Redirect::route('activity-map.import', $project);
    }
}
