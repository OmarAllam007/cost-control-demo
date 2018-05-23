<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Import\QtySurvey\QtySurveyModify;
use App\Project;
use function compact;
use Illuminate\Http\Request;

class ImportSurveyController extends Controller
{
    function edit(Project $project)
    {
        $this->authorize('qty_survey', $project);

        return view('survey.modify', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        $this->authorize('qty_survey', $project);

        $this->validate($request, [
            'file' => ['required', 'file', 'mimes:xlsx']
        ]);

        $importer = new QtySurveyModify($project, $request->file('file'));
        $result = $importer->import();

        flash("{$result['success']} Items have been imported", 'success');

        if (!empty($result['failed'])) {
            return view('survey.import-failed', ['project' => $project, 'failed' => $result['failed']]);
        }

        return redirect()->route('project.budget', $project);
    }
}