<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\BreakDownResourceShadow;
use App\ResourceCode;
use Carbon\Carbon;
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
        $importer = new CostImporter($this->batch);
        $result = $importer->checkPhysicalQty();
        $errors = $result['errors'];

        $hashes = $errors->pluck('rows.*.hash')->flatten();
        $this->rows = $this->rows->forget($hashes->toArray());

        $resources = BreakDownResourceShadow::whereIn('id', array_keys($data))->get()->keyBy('id');
        $resourcesLog = collect();
        foreach ($data as $id => $qty) {
            $hash = str_random(6);
            $resource = $resources[$id];

            $rows = $errors[$id]['rows'];
            $total = $rows->sum(6);
            $unit_price = $total / $qty;
            $doc_no = $rows->pluck(8)->unique()->implode(', ');

            $newResource = [
                $resource->code, '',
                $resource->resource_name, $resource->measure_unit,
                $qty, $unit_price, $total,
                $resource->resource_code,
                $doc_no
            ];

            $this->rows->put($hash, $newResource);
            $resourcesLog->push(compact('resource', 'rows', 'newResource'));
        }

        (new CostIssuesLog($this->batch))->recordPhysicalQuantity($resourcesLog);

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
        return $importer->save();
    }
}