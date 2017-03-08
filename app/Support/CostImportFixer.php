<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\ResourceCode;
use App\WbsResource;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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

        if ($result['error'] != 'physical_qty') {
            return $result;
        }

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

        $issueLog = new CostIssuesLog($this->batch);
        $issueLog->recordClosedResources($closedLog);

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkMultipleCostAccounts();
    }

    function fixMultipleCostAccounts($data)
    {
        $result = (new CostImporter($this->batch))->checkMultipleCostAccounts();
        $errors = $result['errors'];

        $distributionLog = collect();

        foreach ($errors as $error) {
            $date =$error[1];
            $resources = $error['resources']->keyBy('breakdown_resource_id');
            $logEntry = ['oldRow' => $this->rows->get($error['hash']), 'newRows' => []];
            $this->rows->forget($error['hash']);
            $unit_price = floatval($error[5]);
            foreach ($resources as $id => $resource) {
                if (isset($data[$id]) && !empty($data[$id]['included']) && $data[$id]['qty']) {
                    $qty = floatval($data[$id]['qty']);
                    $total = $qty * $unit_price;

                    $newRow = [
                        $resource->code, $date, $resource->resource_name, $resource->measure_unit,
                        $qty, $unit_price, $total, $resource->resource_code, $error[8] ?? '',
                        'resource' => $resource
                    ];

                    $this->rows->push($newRow);
                    $logEntry['newRows'][] = $newRow;
                }
            }

            $distributionLog->push($logEntry);
        }

        $issueLog = new CostIssuesLog($this->batch);
        $issueLog->recordCostAccountDistribution($distributionLog);

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->save();
    }

    function fixProgress($progress)
    {
        $result = (new CostImporter($this->batch))->checkProgress();
        $resources = $result['errors']->keyBy('breakdown_resource_id');

        $progressLog = collect();
        foreach ($progress as $id => $value) {
            $resource = $resources[$id];
            $data = ['progress' => $value];
            if ($value == 100) {
                $data['status'] = 'Closed';
            }
            unset($resource->cost);
            $resource->update($data);
            $cost = CostShadow::where('breakdown_resource_id', $id)->where('period_id', $this->batch->period_id)->first();
            $log = ['resource' => $resource, 'remaining_qty' => $cost->remaining_qty, 'to_date_qty' => $cost->to_date_qty];
            $progressLog->push($log);
        }

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordProgress($progressLog);

        $importer = new CostImporter($this->batch, $this->rows);
        return $importer->checkStatus();
    }

    function fixStatus($status)
    {
        $result = (new CostImporter($this->batch))->checkStatus();
        $resources = $result['errors']->keyBy('breakdown_resource_id');

        $statusLog = collect();
        foreach ($status as $id => $value) {
            $resource = $resources[$id];
            $resource->status = $value;
            if (strtolower($value) == 'closed') {
                $resource->progress = 100;
            }
            $resource->save();
            $cost = CostShadow::where('breakdown_resource_id', $id)->where('period_id', $this->batch->period_id)->first();
            $log = ['resource' => $resource, 'remaining_qty' => $cost->remaining_qty, 'to_date_qty' => $cost->to_date_qty];
            unset($resource->cost, $resource->imported_cost);
            $statusLog->push($log);
        }

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordStatus($statusLog);

        return ['success' => $resources->count(), 'batch' => $this->batch];
    }
}