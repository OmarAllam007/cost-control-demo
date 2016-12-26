<?php

namespace App\Http\Controllers;

use App\ActualResources;
use App\BreakDownResourceShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Jobs\ImportMaterialDataJob;
use App\Project;
use Carbon\Carbon;
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
        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        $file = $request->file('file');

        $result = $this->dispatch(new ImportActualMaterialJob($project, $file->path()));

        if ($result['hasIssues']) {
            flash('Could not import some materials');

            $key = 'mat_' . time();
            \Cache::add($key, $result, 180);

            if (count($result['mapping']) || count($result['resources'])) {
                return \Redirect::route('actual-material.mapping', $key);
            } elseif (count($result['units'])) {
                return \Redirect::route('actual-material.units', $key);
            } else {
                return \Redirect::route('actual-material.multiple', $key);
            }
        }

        flash($result['success'] . ' Records have been imported', 'success');
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
            'project' => $data['project']
        ];

        \Cache::put($key, $data_to_cache);

        if ($data_to_cache['units']->count()) {
            return \Redirect::route('actual-material.units', $key);
        } elseif ($data_to_cache['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } else {
            \Cache::forget($key);
            flash($data_to_cache['success'] . ' records has been imported', 'success');
            return \Redirect::route('project.cost-control', $data_to_cache['project']);
        }
    }

    function fixUnits($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        return view('actual-material.fix-units', $data);
    }

    function postFixUnits($key, Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $newActivities = collect();
        $units = $request->get("units");
//        dd($data['units']->toArray());
        foreach ($data['units'] as $idx => $row) {

            $new_data = $units[$idx];
            $row[10] = $new_data['qty'];
            $row[11] = abs($row[12])/abs($new_data['qty']);
            $row[9] = $row['resource']->measure_unit;

            $newActivities->push($row);
        }

        $result = dispatch(new ImportMaterialDataJob($data['project'], $newActivities));

        $data_to_cache = [
            'success' => $result['success'] + $data['success'],
            'multiple' => $data['multiple']->merge($result['multiple']),
            'project' => $data['project']
        ];

        \Cache::put($key, $data_to_cache);
        if ($data_to_cache['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } else {
            \Cache::forget($key);
            flash($data_to_cache['success'] . ' records has been imported', 'success');
            return \Redirect::route('project.cost-control', $data_to_cache['project']);
        }
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

    function postFixMultiple($key,Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $project = $data['project'];
        $requestResources = $request->get('resource');
        $excelBaseDate = Carbon::create(1899, 12, 30);

        foreach ($data['multiple'] as $activityCode => $resources) {
            foreach ($resources as $resourceCode => $resource) {
                foreach ($resource['resources'] as $shadow) {
                    $material = $requestResources[$activityCode][$resourceCode][$shadow->breakdown_resource_id];
                    if (empty($material['included'])) {
                        continue;
                    }

                    ActualResources::create([
                        'project_id' => $project->id,
                        'wbs_level_id' => $shadow->wbs_id,
                        'breakdown_resource_id' => $shadow->breakdown_resource_id,
                        'period_id' => $project->open_period()->id,
                        'qty' => $material['qty'],
                        'original_code' => $resource[13],
                        'resource_id' => $shadow->resource_id,
                        'unit_price' => $resource[11],
                        'cost' => abs($resource[12]),
                        'unit_id' => $shadow->unit_id,
                        'action_date' => $excelBaseDate->addDays($resource[5])
                    ]);

                    $data['success']++;
                }
            }

        }

        flash($data['success'] . ' records has been imported', 'success');
        return \Redirect::route('project.cost-control', $data['project']);
    }

    function progress()
    {

    }

    function postProgress()
    {

    }

    function status()
    {

    }

    function postStatus()
    {

    }
}
