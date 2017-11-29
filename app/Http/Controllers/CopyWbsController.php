<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectUser;
use App\WbsLevel;
use Illuminate\Http\Request;

class CopyWbsController extends Controller
{
    function create(WbsLevel $wbs_level)
    {
        $projects = Project::where('id', "!=", $wbs_level->project_id)
            ->orderBy('client_name')->orderBy('name')
            ->get()->filter(function ($project) {
                return can('wbs', $project) && can('breakdown', $project) &&
                    can('resources', $project) && can('breakdown_templates', $project) &&
                    can('productivity', $project);
            })->groupBy('client_name')->map(function ($group) {
                return $group->pluck('name', 'id');
            });

        return view('wbs-level.copy-to-project', compact('wbs_level', 'projects'));
    }

    function store(WbsLevel $wbs_level, Request $request)
    {
        $this->validate($request, ["project_id" => "required|exists:projects,id,id,!{$wbs_level->project_id}|has_copy_permission"]);

        $project_id = $request->input('project_id');
        $wbs_level->copyToProject($project_id);

        $project = Project::find($project_id);

        flash('WBS has been copied to ' . $project->name, 'success');

        if ($request->exists('iframe')) {
            return \Redirect::to('/blank');
        }

        return \Redirect::route('project.budget', $wbs_level->project_id);
    }
}
