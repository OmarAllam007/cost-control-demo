<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\BreakDownResourceShadow;
use App\ResourceCode;
use Illuminate\Support\Collection;

class CostImportFixer
{
    /** @var ActualBatch */
    protected $batch;

    /** @var Collection */
    protected $rows;

    function __construct(ActualBatch $batch)
    {
        $this->batch = $batch;

        $key = 'batch_' . $batch->id;
        $data = \Cache::get($key);
        $this->rows = $data['rows'];
    }

    function fixMappingUnprivileged($data)
    {
        $this->rows = $this->rows->filter(function ($row) use ($data) {
            $activityCode = trim(strtolower($row[0]));
            $resourceCode = trim(strtolower($row[7]));
            return !$data['activity']->contains($activityCode) && !$data['resources']->contains($resourceCode);
        });

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkPhysicalQty();
    }

    function fixMappingPrivileged($data)
    {
        $activityLog = collect(['ignored' => collect(), 'mapping' => collect()]);
        foreach ($data['activity'] as $storeCode => $appCode) {
            if ($appCode) {
                $activityLog['mapping']->put($storeCode, $appCode);
                ActivityMap::updateOrCreate(['activity_code' => $appCode, 'equiv_code' => $storeCode, 'project_id' => $this->batch->project->id]);
            } else {
                $code = trim(strtolower($storeCode));
                $activityLog['ignored']->push($code);
            }
        }

        $resourcesLog = collect(['ignored' => collect(), 'mapping' => collect()]);
        foreach ($data['resources'] as $storeCode => $resource_id) {
            if ($resource_id) {
                $activityLog['mapping']->put($storeCode, $resource_id);
                ResourceCode::updateOrCreate(['code' => $storeCode, 'resource_id' => $resource_id, 'project_id' => $this->batch->project->id]);
            } else {
                $code = trim(strtolower($storeCode));
                $activityLog['ignored']->push($code);
            }
        }

        $issueLog = new CostIssuesLog($this->batch);
        $issueLog->recordActivityMappingPrivileged($activityLog);
        $issueLog->recordResourceMappingPrivileged($resourcesLog);

        $this->rows = $this->rows->filter(function ($row) use ($activityLog, $resourcesLog) {
            $activityCode = trim(strtolower($row[0]));
            $resourceCode = trim(strtolower($row[7]));
            return !$activityLog['ignored']->contains($activityCode) && !$resourcesLog['ignored']->contains($resourceCode);
        });
        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkPhysicalQty();
    }

    function fixPhysicalQuantity($data)
    {

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkClosed();
    }

    function fixClosed($data)
    {
        $closedLog = collect(['ignored' => collect(), 'reopened' => collect()]);
        foreach ($data as $id => $status) {
            $resource = BreakDownResourceShadow::find($id);
            if ($status['open']) {
                $resource->status = 'In Progress';
                $resource->progress = $status['progress'];
                $resource->save();
                $closedLog['reopened']->push($resource);
            } else {
                $closedLog['ignored']->push($resource);
            }
        }

        $issueLog = new CostIssuesLog($data['batch']);
        $issueLog->recordClosedResources($closedLog);

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkMultipleCostAccounts();
    }

    function fixMultipleCostAccounts($data)
    {

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->saveAndCheckProgress();
    }

}