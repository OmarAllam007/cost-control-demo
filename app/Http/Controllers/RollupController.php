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
        dd(compact('project', 'wbsLevel', 'stdActivity'));
    }

    function store(Project $project, WbsLevel $wbsLevel, StdActivity $stdActivity, Request $request)
    {

    }
}
