<?php

namespace App\Http\Controllers\Api;

use App\BreakdownTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Project;
use App\StdActivity;
use Illuminate\Http\Request;

class BreakdownTemplateController extends Controller
{
    function index()
    {
        $activityId = request('activity');
        $activity = StdActivity::find($activityId);

        if (!$activity) {
            return [];
        }
        return $activity->breakdowns()->where('project_id',request('project_id'))->orderBy('name')->pluck('name', 'id');
    }

    function templates(Project $project){

        return BreakdownTemplate::orderBy('name')->where('project_id', $project->id)->get()->map(function (BreakdownTemplate $template){
            return $template->morphToJSON();
        });
    }
}
