<?php

namespace App\Http\Controllers;

use App\Import\ModifyBreakdown\Export;
use App\Project;
use Illuminate\Http\Request;

class ModifyBreakdownController extends Controller
{
    function index(Project $project)
    {
        $this->authorize('budget_owner', $project);

        $file = (new Export($project))->handle();
        return \Response::download($file, "modify_breakdowns_" . slug($project->name) . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    function edit(Project $project)
    {
        $this->authorize('budget_owner', $project);

        return view('modify-breakdown.import', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('budget_owner', $project);

        $this->validate($request, ['file' => 'required|mimes:xlsx,xls,csv']);

//        $importer =
    }
}
