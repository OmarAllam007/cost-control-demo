<?php

namespace App\Http\Controllers;

use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Jobs\ImportMaterialDataJob;
use App\Jobs\UpdateResourceDictJob;
use App\Project;
use App\WbsLevel;
use App\WbsResource;
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

        $key = 'mat_' . time();
        \Cache::add($key, $result, 180);

        if ($result['hasIssues']) {
            flash('Could not import some materials');

            if (count($result['mapping']) || count($result['resources'])) {
                return \Redirect::route('actual-material.mapping', $key);
            } elseif (count($result['units'])) {
                return \Redirect::route('actual-material.units', $key);
            } elseif ($result['multiple']) {
                return \Redirect::route('actual-material.multiple', $key);
            } else {
                return \Redirect::route('actual-material.resources', $key);
            }
        }

        flash($result['success'] . ' Records have been imported', 'success');
        return \Redirect::route('actual-material.progress', $key);
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

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newActivities, $data['bastch']));

        $data_to_cache = [
            'success' => $result['success'] + $data['success'],
            'multiple' => $data['multiple']->merge($result['multiple']),
            'units' => $data['units']->merge($result['units']),
            'resources' => $data['resources']->merge($result['resources']),
            'project' => $data['project'],
            'batch' => $data['batch']
        ];

        \Cache::put($key, $data_to_cache);

        if ($data_to_cache['units']->count()) {
            return \Redirect::route('actual-material.units', $key);
        } elseif ($data_to_cache['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } else {
            \Cache::forget($key);
            flash($data['success'] . ' records has been imported', 'success');
            return \Redirect::route('actual-material.progress', $key);
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
            $row[11] = abs($row[12]) / abs($new_data['qty']);
            $row[9] = $row['resource']->measure_unit;

            $newActivities->push($row);
        }

        $result = dispatch(new ImportMaterialDataJob($data['project'], $newActivities, $data['batch']));

        $data_to_cache = [
            'success' => $result['success'] + $data['success'],
            'multiple' => $data['multiple']->merge($result['multiple']),
            'resources' => $data['resources']->merge($result['resources']),
            'project' => $data['project'],
            'batch' => $data['batch']
        ];

        \Cache::put($key, $data_to_cache);
        if ($data_to_cache['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } else {
            \Cache::forget($key);
            flash($data['success'] . ' records has been imported', 'success');
            return \Redirect::route('actual-material.progress', $key);
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

    function postFixMultiple($key, Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $project = $data['project'];
        $requestResources = $request->get('resource');
        $excelBaseDate = Carbon::create(1899, 12, 30);

        $batch_id = $data['batch']->id;
        $resource_dict = collect();

        foreach ($data['multiple'] as $activityCode => $resources) {
            foreach ($resources as $resourceCode => $resource) {
                foreach ($resource['resources'] as $shadow) {
                    $material = $requestResources[$activityCode][$resourceCode][$shadow->breakdown_resource_id];
                    if (empty($material['included'])) {
                        continue;
                    }

                    $resource = ActualResources::create([
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
                        'action_date' => $excelBaseDate->addDays($resource[5]),
                        'batch_id' => $batch_id
                    ]);

                    $resource_dict->push($resource);

                    $data['success']++;
                }
            }
        }

        $this->dispatch(new UpdateResourceDictJob($data['project'], $resource_dict));

        unset($data['multiple']);
        \Cache::put($key, $data);
        if ($data['resources']->count()) {
            return \Redirect::route('actual-material.resources', $key);
        } else {
            \Cache::forget($key);
            flash($data['success'] . ' records has been imported', 'success');
            return \Redirect::route('actual-material.progress', $key);
        }
    }

    function progress($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $resource_ids = CostShadow::select('csh.breakdown_resource_id')->from('cost_shadows as csh')->join('break_down_resource_shadows as bsh', 'bsh.breakdown_resource_id', '=', 'csh.breakdown_resource_id')->where('batch_id', $data['batch']->id)->whereRaw('csh.to_date_qty > bsh.budget_unit')->pluck('breakdown_resource_id', 'breakdown_resource_id');
        $resources = WbsResource::joinShadow()->whereIn('wbs_resources.breakdown_resource_id', $resource_ids)->get()->groupBy(function($resource){
            $wbs = WbsLevel::find($resource->wbs_id);
            return $wbs->name . ' / ' . $resource->activity;
        });

        if (!$resources->count()) {
            return \Redirect::route('actual-material.status', $key);
        }

        return view('actual-material.progress', compact('key', 'resources'));
    }

    function postProgress(Request $request, $key)
    {
        $this->validate($request, ['progress.*' => 'required|numeric|between:0,100'], [
            'required' => 'This field is required', 'numeric' => 'Please enter a numeric value', 'between' => 'Value must be between 0 and 100'
        ]);

        $progress = collect($request->get('progress'));
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $progress->keys())->get()->keyBy('breakdown_resource_id');
        foreach ($progress as $id => $value) {
            $resources[$id]->progress = $value;
            $resources[$id]->save();
        }

        flash('Progress has been updated', 'success');
        return \Redirect::route('actual-material.status', $key);
    }

    function status($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $resource_ids = CostShadow::select('csh.breakdown_resource_id')->from('cost_shadows as csh')->join('break_down_resource_shadows as bsh', 'bsh.breakdown_resource_id', '=', 'csh.breakdown_resource_id')->where('batch_id', $data['batch']->id)->pluck('breakdown_resource_id', 'breakdown_resource_id');
        $resources = WbsResource::joinShadow()->whereIn('wbs_resources.breakdown_resource_id', $resource_ids)->get()->groupBy(function($resource){
            $wbs = WbsLevel::find($resource->wbs_id);
            return $wbs->name . ' / ' . $resource->activity;
        });

        return view('actual-material.status', compact('resources'));
    }

    function postStatus($key)
    {

    }

    function resources($key)
    {

    }

    function postResources($key)
    {

    }
}
