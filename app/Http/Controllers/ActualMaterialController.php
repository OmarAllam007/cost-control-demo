<?php

namespace App\Http\Controllers;

use App\ActivityMap;
use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Export\ExportCostShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Jobs\ImportMaterialDataJob;
use App\Jobs\UpdateResourceDictJob;
use App\Project;
use App\ResourceCode;
use App\WbsLevel;
use App\WbsResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Http\UploadedFile;

class ActualMaterialController extends Controller
{
    function import(Project $project)
    {
        return view('actual-material.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $filename = $file->move(storage_path('batches'), uniqid().'.'.$file->clientExtension());

        $result = $this->dispatch(new ImportActualMaterialJob($project, $filename));

        $key = 'mat_' . time();
        \Cache::add($key, $result, 180);

        return $this->redirect($result, $key);
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
                foreach ($data['mapping']['activity'] as $activity) {
                    if ($activity[0] == $code) {
                        $activity[0] = $activityData['activity_code'];
                        ActivityMap::updateOrCreate(['activity_code' => $activityData['activity_code'], 'equiv_code' => $code, 'project_id' => $data['project']->id]);
                        $newActivities->push($activity);
                    }
                }
            }

        }
            // Issue has been resolved remove from cached data
            $data['mapping']['activity'] = collect();

        if ($request->has('resources')) {
            foreach ($request->get('resources') as $code => $resourceData) {
                foreach ($data['mapping']['resources'] as $activity) {
                    if ($activity[7] == $code) {
                        $activity[7] = $resourceData['resource_code'];
                        $resource = Resource::where(['resource_code' => $resourceData['resource_code'], 'project_id' => $data['project']->id])->first();
                        ResourceCode::updateOrCreate(['project_id' => $data['project']->id, 'code' => $activity[7], 'resource_id' => $resource->id]);
                        $newActivities->push($activity);
                    }
                }
            }

        }
            // Issue has been resolved remove from cached data
            $data['mapping']['resources'] = collect();

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newActivities, $data['batch']));
        return $this->redirect($this->merge($data, $result), $key);
    }

    function fixUnits($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $units = $data['units']->groupBy(function ($row) {
            return $row['resource']->wbs->name . ' / ' . $row['resource']->activity;
        });

        return view('actual-material.fix-units', ['units' => $units, 'project' => $data['project']]);
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
        $dataUnits = $data['units']->keyBy('resource.breakdown_resource_id');
        foreach ($dataUnits as $idx => $row) {
            $new_data = $units[$idx];
            $row[4] = $new_data['qty'];
            if ($new_data['qty'] != 0) {
                $row[5] = $row[6] / $new_data['qty'];
            } else {
                $row[5] = 0;
                $row[6] = 0;
            }
            $row[3] = $row['resource']->measure_unit;

            $newActivities->push($row);
        }

        $data['units'] = collect();
        $result = dispatch(new ImportMaterialDataJob($data['project'], $newActivities, $data['batch']));

        return $this->redirect($this->merge($data, $result, $key), $key);
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

        $requestResources = $request->get('resource');
        $newResources = collect();

        foreach ($data['multiple'] as $activityCode => $resources) {
            foreach ($resources as $resourceCode => $resource) {
                foreach ($resource['resources'] as $shadow) {
                    $material = $requestResources[$activityCode][$resourceCode][$shadow->breakdown_resource_id];
                    if (empty($material['included'])) {
                        continue;
                    }

                    $newResource = $resource;
                    $newResource[4] = $material['qty'];
                    $newResource['resource'] = $shadow;
                    $newResources->push($newResource);
                }
            }
        }
        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newResources, $data['batch']));
        $data['multiple'] = collect();

        return $this->redirect($this->merge($data, $result), $key);
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

        $project = $data['project'];
        return view('actual-material.progress', compact('key', 'resources', 'project'));
    }

    function postProgress(Request $request, $key)
    {
        $this->validate($request, ['progress.*' => 'required|numeric|gt:0|lte:100'], [
            'required' => 'This field is required', 'numeric' => 'Please enter a numeric value',
            'between' => 'Value must be between 0 and 100', 'gt' => 'Value must be greater than 0',
            'lte' => 'Value must be less than or equal to 100'
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

        $project = $data['project'];

        return view('actual-material.status', compact('resources', 'project'));
    }

    function postStatus(Request $request, $key)
    {
        $this->validate($request, ['status.*' => 'required'], ['required' => 'This field is required']);

        $status = collect($request->get('status'));
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $status->keys())->get()->keyBy('breakdown_resource_id');
        foreach ($status as $id => $value) {
            $resources[$id]->status = $value;
            $resources[$id]->save();
        }

        $data = \Cache::get($key);
        \Cache::forget($key);

        flash('Status has been updated', 'success');
        return \Redirect::route('project.cost-control', $data['project']);
    }

    function resources($key)
    {

    }

    function postResources($key)
    {

    }

    function closed($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $closed = $data['closed']->pluck('resource')->keyBy('id')->groupBy(function($resource) {
            return $resource->wbs->name . ' / ' . $resource->activity;
        });

        $project = $data['project'];

        return view('actual-material.closed', compact('closed', 'project'));
    }

    function postClosed(Request $request, $key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $closed = $data['closed']->pluck('resource')->keyBy('id');

        $newResourceIds = [];
        foreach ($request->get('closed') as $id => $is_open)
        {
            if ($is_open) {
                $closed[$id]->status = 'In Progress';
                $closed[$id]->save();
                $newResourceIds[] = $id;
            }
        }

        $newResources = $data['closed']->whereIn('resource.id', $newResourceIds)->map(function($row) {
            $row['resource'] = $row['resource']->fresh();
            return $row;
        });

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newResources, $data['batch']));
        $data['closed'] = collect();

        return $this->redirect($this->merge($data, $result), $key);
    }

    function ExportCostBreakdown(Project $project){
        if (\Gate::denies('read', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->dispatch(new ExportCostShadow($project));
    }

    protected function redirect($data, $key)
    {
        \Cache::put($key, $data, 180);

        if ($data['mapping']['activity']->count() || $data['mapping']['resources']->count()) {
            return \Redirect::route('actual-material.mapping', $key);
        } elseif($data['closed']->count()){
            return \Redirect::route('actual-material.closed', $key);
        } elseif($data['units']->count()) {
            return \Redirect::route('actual-material.units', $key);
        } elseif ($data['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } elseif ($data['to_import']->count()) {
            $count = $this->saveImported($data['to_import']);
            flash("$count Records has been imported", 'success');
            return \Redirect::route('actual-material.progress', $key);
        } else {
            flash('No data has been imported');
            return \Redirect::route('project.cost-control', $data['project']);
        }
    }

    protected function saveImported($to_import)
    {
        $count = 0;

        foreach ($to_import as $record) {
            ActualResources::create($record);
            ++$count;
        }

        return $count;
    }

    protected function merge($data, $result)
    {
        $returnData =  [
            'mapping' => [
                'activity' => $data['mapping']['activity']->merge($result['mapping']['activity']),
                'resources' => $data['mapping']['resources']->merge($result['mapping']['resources']),
            ],
            'multiple' => $data['multiple']->merge($result['multiple']),
            'units' => $data['units']->merge($result['units']),
            'resources' => $data['resources']->merge($result['resources']),
            'closed' => $data['closed']->merge($result['closed']),
            'to_import' => $data['to_import']->merge($result['to_import']),
            'project' => $data['project'],
            'batch' => $data['batch']
        ];

        return $returnData;
    }
}
