<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProjectReportsController extends Controller
{
    function show(Project $project)
    {
        return view('project.reports', compact('project'));
    }
}
