<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Jobs\ImportMaterialDataJob;
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

        $newActivities = collect();
        if ($request->has('activity')) {
            foreach ($request->get('activity') as $code => $activityData) {
                foreach ($data['mapping'] as $activity) {
                    if ($activity[3] == $code) {
                        $activity[3] = $activityData['activity_code'];
                        $newActivities->push($activity);
                    }
                }
            }

            // Issue has been resolved remove from cached data
            unset($data['mapping']);
        }

        if ($request->has('resources')) {
            foreach ($request->get('resources') as $code => $resourceData) {
                foreach ($data['resources'] as $activity) {
                    if ($activity[13] == $code) {
                        $activity[13] = $resourceData['resource_code'];
                        $newActivities->push($activity);
                    }
                }
            }

            // Issue has been resolved remove from cached data
            unset($data['resources']);
        }

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newActivities));

        $data_to_cache = [
            'success' => $result['success'] + $data['success'],
            'multiple' => $data['multiple']->merge($result['multiple']),
            'units' => $data['units']->merge($result['units']),
            'invalid' => $data['invalid']->merge($result['invalid']),
            'project' => $data['project']
        ];

        \Cache::put($key, $data_to_cache);

        if ($data_to_cache['units']->count()) {
            return \Redirect::route('actual-material.fix-units', $key);
        } elseif ($data_to_cache['multiple']->count()) {
            return \Redirect::route('actual-material.fix-multiple', $key);
        } else {
            \Cache::forget($key);
            flash($data_to_cache['success'] . ' records has been imported');
            return \Redirect::route('project.cost-control', $data_to_cache['project']);
        }
    }

    function fixUnits()
    {

    }

    function postFixUnits()
    {

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
