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
use App\Support\CostImporter;
use App\Support\CostImportFixer;
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

        return $this->redirect($result);
    }

    function fixMapping(ActualBatch $actual_batch)
    {
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkMapping();
        $data = $result['errors'];

        $issuesLog = new CostIssuesLog($actual_batch);
        if ($data['activity']->count() || $data['resources']->count()) {
            $unprivileged = false;
            if ($data['activity']->count() && cannot('activity_mapping', $actual_batch->project)) {
                $issuesLog->recordActivityMappingUnPrivileged($data['activity']);
                $this->dispatch(new SendMappingErrorNotification($data, 'activity'));
                $data['activity'] = collect();
                $unprivileged = true;
            }

            if ($data['resources']->count() && cannot('resource_mapping', $actual_batch->project)) {
                $issuesLog->recordResourceMappingUnPrivileged($data['resources']);
                $this->dispatch(new SendMappingErrorNotification($data, 'resources'));
                $data['resources'] = collect();
                $unprivileged = true;
            }

            if ($unprivileged) {
                $fixer = new CostImportFixer($actual_batch);
                $result = $fixer->fixMappingUnprivileged($data);

                return $this->redirect($result);
            }
        }

        $data['projectActivityCodes'] = BreakDownResourceShadow::where('project_id', $actual_batch->project_id)
            ->select(['id', 'code', 'wbs_id', 'cost_account', 'activity'])->with('wbs')->get()->keyBy('code');
        $data['project'] = $actual_batch->project;

        return view('actual-material.fix-mapping', $data);
    }

    function postFixMapping(ActualBatch $actual_batch, Request $request)
    {
        $data['activity'] = $request->get('activity', []);
        $data['resources'] = $request->get('resources', []);

        $fixer = new CostImportFixer($actual_batch);
        $result = $fixer->fixMappingPrivileged($data);

        return $this->redirect($result);
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
                $log = ['original' => $resource, 'distributed' => []];
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

                    $log['distributed'][] = $newResource;
                }

                $costAccountLog->push($log);
            }
        }

        $issueLog = new CostIssuesLog($data['batch']);
        $issueLog->recordCostAccountDistribution($costAccountLog);

        $result = $this->dispatch(new ImportMaterialDataJob($data['project'], $newResources, $data['batch']));
        $data['multiple'] = collect();

        return $this->redirect($this->merge($data, $result), $key);
    }

    function progress(ActualBatch $actual_batch)
    {
        $project = $actual_batch->project;
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkClosed();
        $resources = $result['errors']->groupBy(function ($resource) {
            return $resource->wbs->path . ' / ' . $resource->activity;
        })->sortByKeys();

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

        $period_id = $data['project']->open_period()->id;

        $progressLog = collect();
        foreach ($progress as $id => $value) {
            $resource = $resources[$id];
            $resource->progress = $value;
            if ($resource->progress == 100) {
                $resource->status = 'Closed';
            }
            $resource->save();
            $resource->import_cost = WbsLevel::joinBudget()->where('breakdown_resource_id', $resource->breakdown_resource_id)
                ->where('period_id', $period_id)->get()->toArray();
            $progressLog->push($resource);
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

    function resources(ActualBatch $actual_batch)
    {
        $project = $actual_batch->project;
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkPhysicalQty();

        $errors = $result['errors'];
        $activities = $errors->groupBy(function ($error) {
            return $error['resource']->wbs->path . ' / ' . $error['resource']->activity;
        });

        return view('actual-material.resources', compact('project', 'activities'));
    }

    function postResources(ActualBatch $actual_batch, Request $request)
    {
        $fixer = new CostImportFixer($actual_batch);
        $result = $fixer->fixPhysicalQuantity($request->get('quantities', []));

        return $this->redirect($result);
    }

    function closed(ActualBatch $actual_batch)
    {
        $project = $actual_batch->project;
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkClosed();
        $closed = $result['errors']->groupBy(function ($resource) {
            return $resource->wbs->path . ' / ' . $resource->activity;
        })->sortByKeys();

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

        foreach ($request->get('closed', []) as $id => $status) {
            $is_open = $status['open'];
            if ($is_open) {
                $closed[$id]->status = 'In Progress';
                if ($status['progress']) {
                    $closed[$id]->progress = $status['progress'];
                    $rawData[$id]['progress'] = $status['progress'];
                }
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

        $file = $this->dispatch(new ExportCostShadow($project));
        return \Response::download($file, slug($project->name) . '_actual_cost.csv', ['Content-Type: text/csv']);
    }

    protected function redirect($result)
    {
        $batch = $result['batch'];
        $key = $batch->id;

        if (!empty($result['error'])) {
           switch($result['error']) {
               case 'mapping':
                   return \Redirect::route('actual-material.mapping', $key);
               case 'physical_qty':
                   return \Redirect::route('actual-material.resources', $key);
               case 'closed':
                   return \Redirect::route('actual-material.closed', $key);
               case 'cost_accounts':
                   return \Redirect::route('actual-material.multiple', $key);
               case 'progress':
                   return \Redirect::route('actual-material.progress', $key);
               case 'status':
                   return \Redirect::route('actual-material.status', $key);
           }
       } else {
            flash("{$result['success']} resources has been updated");
            return \Redirect::route('project.cost-control', $batch->project);
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
            'batch' => $data['batch'],
            'invalid' => $data['invalid']->mergeWithKeys($result['invalid'])
        ];

        file_put_contents(storage_path('logs/debug.log'), print_r($returnData, true));

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
