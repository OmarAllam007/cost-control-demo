<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use App\Rollup\Export\ExportBreakdownTemplates;
use App\Rollup\Import\BreakdownTemplateImporter;

class BreakdownTemplateExportModifyController extends Controller
{
    function index(Request $request)
    {
        $project = $this->handlePermissions($request, 'read');

        $exporter = new ExportBreakdownTemplates($project);
        $filename = $exporter->handle();
        
        return \Response::download($filename, basename($filename))->deleteFileAfterSend(true);
    }

    function edit(Request $request)
    {
        $project = $this->handlePermissions($request, 'write');

        return view('breakdown-template.modify', compact('project'));
    }

    function update(Request $request)
    {
        $project = $this->handlePermissions($request, 'write');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');

        $importer = new BreakdownTemplateImporter($file, $project);
        $result = $importer->handle();

        if ($result['failed']->count()) {
            return view('breakdown-template.failed-modify', $result);
        } 

        flash("{$result['success']} records have been imported", 'success');
        if ($project) {
            return \Redirect::route('project.budget', $project);
        }
        
        return \Redirect::route('breakdown-template.index');    
    }
    private function handlePermissions(Request $request, $ability)
    {
        if ($project_id = $request->get('project')) {
            $project = Project::findOrFail($project_id);
            $this->authorize('budget_owner', $project);
        } else {
            $project = null;
            $this->authorize('read', 'breakdown-template');
        }

        return $project;
    }
}