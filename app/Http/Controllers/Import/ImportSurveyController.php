<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Project;
use Illuminate\Http\Request;

class ImportSurveyController extends Controller
{
    function edit(Project $project)
    {
        $this->authorize('qty_survey', $project);

        return view('survey._modify');
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('qty_survey', $project);

        $this->validate($request, [
            'file' => ['required', 'file', 'mimes:xlsx']
        ]);

        $importer = new \App\Import\QtySurvey\QtySurveyModify($project, $request->file('file'));
        return $importer->import;
    }
}