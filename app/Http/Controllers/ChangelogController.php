<?php

namespace App\Http\Controllers;

use App\Change;
use App\ChangeLog;
use App\Project;
use Illuminate\Http\Request;

class ChangelogController extends Controller
{
    function show(Project $project)
    {
        dd(Change::forProject($project)->paginate());
        return view('changelog.show', compact('project'));
    }
}
