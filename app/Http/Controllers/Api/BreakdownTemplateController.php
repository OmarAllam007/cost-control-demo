<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
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
}
