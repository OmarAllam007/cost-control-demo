<?php

namespace App\Http\Controllers;

use App\Project;
use App\StdActivity;
use App\WbsLevel;
use Illuminate\Http\Request;

class RollupController extends Controller
{
    function create(Project $project, WbsLevel $wbsLevel, StdActivity $stdActivity)
    {
        if (cannot('actual_resources', $project)) {
            return ["ok" => false, 'message' => 'You are not authorized to do this action'];
        }


    }

    function store(Project $project, WbsLevel $wbsLevel, StdActivity $stdActivity, Request $request)
    {

    }
}
