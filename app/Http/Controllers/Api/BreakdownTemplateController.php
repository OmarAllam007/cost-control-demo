<?php

namespace App\Http\Controllers\Api;

use App\ActivityDivision;
use App\BreakdownTemplate;
use App\Http\Controllers\Controller;
use App\Project;
use App\StdActivity;
use function request;

class BreakdownTemplateController extends Controller
{
    function index()
    {
        return BreakdownTemplate::when(request('project_id'), function($q) {
            return $q->where('project_id',request('project_id'));
        }, function($q) {
            return $q->whereNull('project_id');
        })->when(request('activity'), function($q) {
            return $q->where('std_activity_id', request('activity'));
        })->when(request('division'), function($q) {
            $division = ActivityDivision::find(request('division'));
            if ($division) {
                $activities = StdActivity::whereIn('division_id', $division->getChildrenIds())->pluck('id');
                $q->whereIn('std_activity_id', $activities);
            }
            return $q;
        })->when(request('term'), function($q) {
            return $q->where(function($q) {
                $term = '%' . request('term') . '%';
                $q->where('name', 'like', $term)->orWhere('code', 'like', $term);
            });
        })->orderBy('name')->select('name', 'id', 'code')->paginate(50);
    }

    function templates(Project $project){
        return BreakdownTemplate::orderBy('name')->where('project_id', $project->id)->get()->map(function (BreakdownTemplate $template){
            return $template->morphToJSON();
        });
    }
}
