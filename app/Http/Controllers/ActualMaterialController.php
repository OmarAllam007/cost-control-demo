<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class ActualMaterialController extends Controller
{
    function import(Project $project)
    {
        return view('actual-material.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $result = $this->dispatch(new ImportActualMaterialJob($project, $file->path()));

        if ($result['hasIssues']) {
            flash('Could not import some materials');

            $key = 'mat_' . time();
            \Cache::add($key, $result, 180);

            if (count($result['mapping']) || count($result['resources']) || count($result['units'])) {
                return \Redirect::route('actual-material.mapping', $key);
            } else {
                return \Redirect::route('actual-material.multiple', $key);
            }
        }

        flash($result['count'] . ' Records have been imported', 'success');
        return \Redirect::route('project.cost-control', $project);
    }

    function fixMapping($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $data['projectActivityCodes'] = $data['project']->breakdown_resources->load([
            'breakdown', 'breakdown.std_activity', 'breakdown.wbs_level'
        ])->keyBy('code');

        return view('actual-material.fix-mapping', $data);
    }

    function postFixMapping($key, Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        dd($request->all());

        return \Redirect::route('project.cost-control', $data['project_id']);
    }

    function fixMultiple($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        return view('actual-material.fix-multiple', $data);
    }

    function postFixMultiple($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }
    }
}
