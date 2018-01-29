<?php

namespace App\Http\Controllers\Rollup;

use App\Http\Controllers\Controller;
use App\Project;
use Illuminate\Http\Request;

class RollupController extends Controller
{
    function create(Project $project)
    {
        // We get WBS levels from WbsComposer
        return view('rollup.create', compact('project'));
    }

    function store(Project $project, Request $request)
    {

    }

    function edit(Project $project)
    {
        return view('rollup.edit', compact('project'));
    }

    function update(Project $project, Request $request)
    {

    }
}
