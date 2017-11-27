<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ChangelogController extends Controller
{
    function show(Project $project)
    {

        return view('changelog.show', compact('project'));
    }
}
