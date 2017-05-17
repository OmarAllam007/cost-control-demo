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
use App\Jobs\NotifyCostOwnerForUpload;
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
use Illuminate\Http\Response;
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
                $this->dispatch(new SendMappingErrorNotification($actual_batch->project, $data, 'activity'));
                $data['activity'] = collect();
                $unprivileged = true;
            }

            if ($data['resources']->count() && cannot('resource_mapping', $actual_batch->project)) {
                $issuesLog->recordResourceMappingUnPrivileged($data['resources']);
                $this->dispatch(new SendMappingErrorNotification($actual_batch->project, $data, 'resources'));
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
        $this->validate($request, ['quantities.*' => 'gt:0']);
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

    function postClosed(ActualBatch $actual_batch, Request $request)
    {
        $fixer = new CostImportFixer($actual_batch);
        $result = $fixer->fixClosed($request->get('closed', []));

        return $this->redirect($result);
    }

    function fixMultiple(ActualBatch $actual_batch)
    {
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkMultipleCostAccounts();

        $project = $actual_batch->project;
        $resources = $result['errors']->groupBy(function ($error) {
            $resource = $error['resources'][0];
            return $resource->wbs->path . ' / ' . $resource->activity;
        })->sortByKeys();

        return view('actual-material.fix-multiple', compact('project', 'resources'));
    }

    function postFixMultiple(ActualBatch $actual_batch, Request $request)
    {
        $data = $request->get('resource');
        $result = (new CostImportFixer($actual_batch))->fixMultipleCostAccounts($data);

        return $this->redirect($result);
    }

    function progress(ActualBatch $actual_batch)
    {
        $project = $actual_batch->project;
        $importer = new CostImporter($actual_batch);
        $result = $importer->checkProgress();

        $resources = $result['errors']->groupBy(function ($resource) {
            return $resource->wbs->path . ' / ' . $resource->activity;
        })->sortByKeys();

        return view('actual-material.progress', compact('key', 'resources', 'project'));
    }

    function postProgress(ActualBatch $actual_batch, Request $request)
    {
        $this->validate($request, ['progress.*' => 'required|numeric|gt:0|lte:100'], [
            'required' => 'This field is required', 'numeric' => 'Please enter a numeric value',
            'between' => 'Value must be between 0 and 100', 'gt' => 'Value must be greater than 0',
            'lte' => 'Value must be less than or equal to 100'
        ]);

        $result = (new CostImportFixer($actual_batch))->fixProgress($request->get('progress'));
        return $this->redirect($result);
    }

    function status(ActualBatch $actual_batch)
    {
        $result = (new CostImporter($actual_batch))->checkStatus();

        $resources = $result['errors']->groupBy(function ($resource){
            return $resource->wbs->path . ' / ' . $resource->activity;
        })->sortByKeys();
        $project = $actual_batch->project;

        return view('actual-material.status', compact('resources', 'project'));
    }

    function postStatus(ActualBatch $actual_batch, Request $request)
    {
        $this->validate($request, ['status.*' => 'required'], ['required' => 'This field is required']);
        $result = (new CostImportFixer($actual_batch))->fixStatus($request->get('status'));

        $this->dispatch(new NotifyCostOwnerForUpload($actual_batch));

        return $this->redirect($result);
    }

    function exportCostBreakdown(Project $project, Request $request)
    {
        if (cannot('cost_control', $project)) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $content = $this->dispatch(new ExportCostShadow($project, $request->get('perspective', '')));
        return new Response($content, 200, [
            'Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=' . slug($project->name) . '_actual_cost.csv'
        ]);
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
               case 'no_resources':
                   flash('No resources has been imported. Please check uploaded data', 'warning');
                   $this->dispatch(new NotifyCostOwnerForUpload($batch));
                   return redirect()->route('project.cost-control', $batch->project);
           }
       } else {
            flash("{$result['success']} resources has been updated", 'success');
            return \Redirect::route('project.cost-control', $batch->project);
        }
    }
}
