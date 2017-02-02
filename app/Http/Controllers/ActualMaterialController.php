<?php

namespace App\Http\Controllers;

use App\ActivityMap;
use App\ActualBatch;
use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Export\ExportCostShadow;
use App\Jobs\ImportActualMaterialJob;
use App\Jobs\ImportMaterialDataJob;
use App\Jobs\SendMappingErrorNotification;
use App\Jobs\UpdateResourceDictJob;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\Support\CostIssuesLog;
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
        if (cannot('actual_resources', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.budget', $project);
        }

        return view('actual-material.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        if (cannot('actual_resources', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.budget', $project);
        }

        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $file->move(storage_path('batches'), $filename = uniqid() . '.' . $file->clientExtension());
        $filename = storage_path('batches') . '/' . $filename;

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

        $issuesLog = new CostIssuesLog($data['batch']);
        if ($data['mapping']['activity']->count() || $data['mapping']['resources']->count()) {
            if ($data['mapping']['activity']->count() && cannot('activity_mapping', $data['project'])) {
                $issuesLog->recordActivityMappingUnPrivileged($data['mapping']['activity']);
                $this->dispatch(new SendMappingErrorNotification($data, 'activity'));
                $data['mapping']['activity'] = collect();
            }

            if ($data['mapping']['resources']->count() && cannot('resource_mapping', $data['project'])) {
                $issuesLog->recordResourceMappingUnPrivileged($data['mapping']['resources']);
                $this->dispatch(new SendMappingErrorNotification($data, 'resources'));
                $data['mapping']['resources'] = collect();
            }

            if (!$data['mapping']['activity']->count() && !$data['mapping']['resources']->count()) {
                return $this->redirect($data, $key);
            }
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
        $activityMappingLog = collect();
        if ($request->has('activity')) {
            foreach ($request->get('activity') as $code => $activityData) {
                if (!empty($activityData['skip']) || empty($activityData['activity_code'])) {
                    $activityMappingLog->put($code,  '');
                    continue;
                }

                foreach ($data['mapping']['activity'] as $activity) {
                    if ($activity[0] == $code) {
                        $activity[0] = $activityData['activity_code'];
                        $activityMappingLog->put($code, $activityData['activity_code']);
                        ActivityMap::updateOrCreate(['activity_code' => $activityData['activity_code'], 'equiv_code' => $code, 'project_id' => $data['project']->id]);
                        $newActivities->push($activity);
                    }
                }
            }

        }
        // Issue has been resolved remove from cached data
        $data['mapping']['activity'] = collect();

        $resourceMappingLog = collect();
        if ($request->has('resources')) {
            foreach ($request->get('resources') as $code => $resourceData) {
                if (!empty($resourceData['skip']) || empty($resourceData['resource_code'])) {
                    $resourceMappingLog->put($code, '');
                    continue;
                }

                foreach ($data['mapping']['resources'] as $activity) {
                    if ($activity[7] == $code) {
                        $activity[7] = $resourceData['resource_code'];
                        $resourceMappingLog->put($code, $resourceData['resource_code']);
                        $resource = Resources::where(['resource_code' => $resourceData['resource_code'], 'project_id' => $data['project']->id])->first();
                        ResourceCode::updateOrCreate(['project_id' => $data['project']->id, 'code' => $activity[7], 'resource_id' => $resource->id]);
                        $newActivities->push($activity);
                    }
                }
            }

        }

        // Save the data into error log
        $issueLog = new CostIssuesLog($data['batch']);
        $issueLog->recordActivityMappingPrivileged($activityMappingLog);
        $issueLog->recordResourceMappingPrivileged($resourceMappingLog);

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

//        $units = $data['units']->groupBy(function ($row) {
//            return $row['resource']->wbs->name . ' / ' . $row['resource']->activity . ' / ' . $row['resource']->resource_name;
//        });

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

        foreach ($data['units'] as $idx => $row) {
            $qty = $units[$idx]['qty'];
            $unit_price = $row[6] / $qty;
            $row[3] = $row['unit_resource']->units->type;
            $row[4] = $qty;
            $row[5] = $unit_price;
            $newActivities->push($row);
        }

        $result = dispatch(new ImportMaterialDataJob($data['project'], $newActivities, $data['batch']));
        $data['units'] = collect();

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

        $costAccountLog = collect();
        foreach ($data['multiple'] as $activityCode => $resources) {
            foreach ($resources as $resourceCode => $resource) {
                foreach ($resource['resources'] as $shadow) {
                    $material = $requestResources[$activityCode][$resourceCode][$shadow->breakdown_resource_id];
                    if (empty($material['included'])) {
                        continue;
                    }

                    $newResource = $resource;
                    $newResource[4] = $material['qty'];
                    $newResource[6] = $material['qty'] * $resource[5];
                    $newResource['resource'] = $shadow;
                    $newResources->push($newResource);

                    $costAccountLog->push(compact('resource', 'newResource'));
                }
            }
        }

        $issueLog = new CostIssuesLog($data['batch']);
        $issueLog->recordCostAccountDistribution($costAccountLog);

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
        $resources = WbsResource::joinShadow()->whereIn('wbs_resources.breakdown_resource_id', $resource_ids)->get()->groupBy(function ($resource) {
            $wbs = WbsLevel::find($resource->wbs_id);
            return $wbs->path . ' / ' . $resource->activity;
        });

        if (!$resources->count()) {
            return \Redirect::route('actual-material.status', $key);
        }

        $project = $data['project'];
        return view('actual-material.progress', compact('key', 'resources', 'project'));
    }

    function postProgress(Request $request, $key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $this->validate($request, ['progress.*' => 'required|numeric|gt:0|lte:100'], [
            'required' => 'This field is required', 'numeric' => 'Please enter a numeric value',
            'between' => 'Value must be between 0 and 100', 'gt' => 'Value must be greater than 0',
            'lte' => 'Value must be less than or equal to 100'
        ]);

        $progress = collect($request->get('progress'));
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $progress->keys())->get()->keyBy('breakdown_resource_id');

        $progressLog = collect();
        foreach ($progress as $id => $value) {
            $resources[$id]->progress = $value;
            $resources[$id]->save();
            $progressLog->push($resources[$id]);
        }

        $costIssues = new CostIssuesLog($data['batch']);
        $costIssues->recordProgress($progressLog);

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
        $resources = WbsResource::joinShadow()->whereIn('wbs_resources.breakdown_resource_id', $resource_ids)->get()->groupBy(function ($resource) {
            $wbs = WbsLevel::find($resource->wbs_id);
            return $wbs->path . ' / ' . $resource->activity;
        });

        $project = $data['project'];

        return view('actual-material.status', compact('resources', 'project'));
    }

    function postStatus(Request $request, $key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $this->validate($request, ['status.*' => 'required'], ['required' => 'This field is required']);

        $status = collect($request->get('status'));
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $status->keys())->get()->keyBy('breakdown_resource_id');
        $statusLog = collect();
        foreach ($status as $id => $value) {
            $resources[$id]->status = $value;
            if (strtolower($value) == 'closed') {
                $resources[$id]->progress = 100;
            }

            $resources[$id]->save();
            $statusLog->push($resources[$id]);
        }

        $costIssues = new CostIssuesLog($data['batch']);
        $costIssues->recordStatus($statusLog);

        $data = \Cache::get($key);
        \Cache::forget($key);

        flash('Status has been updated', 'success');
        return \Redirect::route('project.cost-control', $data['project']);
    }

    function resources($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $project = $data['project'];
        $resources = $data['resources'];
        $shadows = $this->getResourcesShadow($resources);

        return view('actual-material.resources', compact('project', 'shadows', 'resources'));
    }

    function postResources(Request $request, $key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $shadows = $this->getResourcesShadow($data['resources']);
        $quantities = $request->get('quantities');
        $newResources = collect();
        $resourcesLog = collect();
        foreach ($data['resources'] as $code => $resources) {
            $code = mb_strtolower($code);
            foreach ($resources as $id => $rows) {
                if (empty($shadows[$code]['resources'][$id])) {
                    continue;
                }

                $shadow = $shadows[$code]['resources'][$id];
                $qty = $quantities[$code][$id];
                $total = collect($rows)->sum('6');

                if (floatval($qty)) {
                    $unit_price = $total / $qty;
                } else {
                    $total = $qty = $unit_price = 0;
                }

                $newResources->push($resource = [
                    $shadow->code, '',
                    $shadow->resource_name, $shadow->measure_unit,
                    $qty, $unit_price, $total, $shadow->resource_code, ''
                ]);

                $resource['rows'] = $rows;

                $resourcesLog->push($resource);
            }
        }

        $issuesLog = new CostIssuesLog($data['batch']);
        $issuesLog->recordPhysicalQuantity($resourcesLog);

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newResources, $data['batch']));
        $data['resources'] = collect();

        return $this->redirect($this->merge($data, $result), $key);
    }

    function closed($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            return \Redirect::route('project.index');
        }

        $closed = $data['closed']->pluck('resource')->keyBy('id')->groupBy(function ($resource) {
            return $resource->wbs->path . ' / ' . $resource->activity;
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
        $rawData = $data['closed']->keyBy('resource.id');

        $newResourceIds = [];
        $resourcesLog = collect(['reopened' => collect(), 'ignored' => collect()]);
        foreach ($request->get('closed', []) as $id => $is_open) {
            if ($is_open) {
                $closed[$id]->status = 'In Progress';
                $closed[$id]->save();
                $newResourceIds[] = $id;
                $resourcesLog->get('reopened')->push($rawData[$id]);
            } else {
                $resourcesLog->get('ignored')->push($rawData[$id]);
            }
        }

        $newResources = $data['closed']->whereIn('resource.id', $newResourceIds)->map(function ($row) {
            $row['resource'] = $row['resource']->fresh();
            return $row;
        });

        $issueLog = new CostIssuesLog($data['batch']);
        $issueLog->recordClosedResources($resourcesLog);

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newResources, $data['batch']));
        $data['closed'] = collect();

        return $this->redirect($this->merge($data, $result), $key);
    }

    function ExportCostBreakdown(Project $project)
    {
        if (cannot('cost_control', $project)) {
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
        } elseif ($data['resources']->count()) {
            return \Redirect::route('actual-material.resources', $key);
        } elseif ($data['closed']->count()) {
            return \Redirect::route('actual-material.closed', $key);
        } elseif ($data['multiple']->count()) {
            return \Redirect::route('actual-material.multiple', $key);
        } elseif ($data['to_import']->count()) {
            $issueLog = new CostIssuesLog(ActualBatch::find($data['batch']));
            $issueLog->recordInvalid($data['invalid']);
            $count = $this->saveImported($data['to_import']);
            $data['to_import'] = collect();
            $data['invalid'] = collect();
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

        $resource_dict = collect();
        foreach ($to_import as $record) {
            ActualResources::create($record);
            $resource_dict->push($record['resource_id']);
            ++$count;
        }

        $project = Project::find($record['project_id']);
        $this->dispatch(new UpdateResourceDictJob($project, $resource_dict));

        return $count;
    }

    protected function merge($data, $result)
    {
        $returnData = [
            'mapping' => [
                'activity' => $data['mapping']['activity']->mergeWithKeys($result['mapping']['activity']),
                'resources' => $data['mapping']['resources']->mergeWithKeys($result['mapping']['resources']),
            ],
            'multiple' => $data['multiple']->mergeWithKeys($result['multiple']),
            'resources' => $data['resources']->mergeWithKeys($result['resources']),
            'closed' => $data['closed']->mergeWithKeys($result['closed']),
            'to_import' => $data['to_import']->mergeWithKeys($result['to_import']),
            'project' => $data['project'],
            'batch' => $data['batch']
        ];

        return $returnData;
    }

    /**
     * @param $resources
     * @return mixed
     */
    protected function getResourcesShadow($resources)
    {
        $activityCodes = $resources->keys();
        $resourceIds = $resources->reduce(function ($ids, $activity) {
            return $ids->merge(array_keys($activity));
        }, collect());

        $shadows = BreakDownResourceShadow::whereIn('code', $activityCodes)->whereIn('resource_id', $resourceIds)
            ->get()->groupBy(function ($item) {
                return mb_strtolower($item->code);
            })->map(function ($shadows) {
                $first = $shadows->first();
                return collect(['name' => $first->wbs->path . ' / ' . $first->activity, 'resources' => $shadows->keyBy('resource_id')]);
            });
        return $shadows;
    }
}
