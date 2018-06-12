<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\ActualResources;
use App\BreakDownResourceShadow;
use App\ResourceCode;
use App\StoreResource;
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
//        $resources = BreakDownResourceShadow::whereIn('id', array_keys($data))->get()->keyBy('id');
        $resourcesLog = collect();
        foreach ($data as $key => $qty) {
            $rows = $errors[$key]['rows'];
            $newResource = $rows->first();

            $newResource[4] = $qty;
            $newResource[6] = $rows->sum(6);
            $newResource[5] = $newResource[6] / $qty;
            $newResource[8] = $rows->pluck(8)->unique()->implode(', ');


            $resource = $errors[$key]['resource'];
            if ($resource->is_rollup || ($resource->rollup_resource_id && $resource->important)) {
                $newResource['resource'] = $resource;
            }

            $row_id = StoreResource::create([
                'project_id' => $this->batch->project->id, 'period_id' => $this->batch->period->id, 'batch_id' => $this->batch->id,
                'activity_code' => $newResource[0], 'store_date' => $newResource[1], 'item_desc' => $newResource[2],
                'measure_unit' => $newResource[3], 'qty' => $newResource[4], 'unit_price' => $newResource[5], 'cost' => $newResource[6],
                'item_code' => $newResource[7], 'doc_no' => $newResource[8], 'row_ids' => $rows->pluck('hash')
            ])->id;

            $newResource['hash'] = $row_id;

            $this->rows->put($row_id, $newResource);
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
            $date = $error[1];
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
                        'original_row_id' => $error['hash'],
                        'resource' => $resource
                    ];

                    $store_resource = StoreResource::forceCreate([
                        'project_id' => $this->batch->project_id, 'period_id' => $this->batch->period_id,
                        'batch_id' => $this->batch->id,
                        'budget_code' => $resource->code, 'resource_id' => $resource->resource_id,
                        'activity_code' => $error[0], 'store_date' => $date, 'item_code' => $error[7],
                        'item_desc' => $error[2], 'measure_unit' => $resource->measure_unit,
                        'unit_price' => $unit_price, 'qty' => $qty, 'cost' => $total, 'doc_no' => $error[8],
                    ]);
                    $newRow['id'] = $store_resource->id;

                    $this->rows->put($newRow['id'], $newRow);
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
            $resource = $resources->get('id');
            if (!$resource) {
                continue;
            }
            $data = ['progress' => $value];
            if ($value == 100) {
                $data['status'] = 'Closed';
            }
            unset($resource->cost);
            $resource->update($data);
            $resource->actual_resources()
                ->orderBy('id', 'desc')->first()
                ->update(['progress' => $resource->progress, 'status' => $resource->status]);

//            $cost = CostShadow::where('breakdown_resource_id', $id)->where('period_id', $this->batch->period_id)->first();
            $log = ['resource' => $resource, 'remaining_qty' => $resource->remaining_qty, 'to_date_qty' => $resource->to_date_qty];
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
        foreach ($status['status'] as $id => $value) {
            $resource = $resources[$id];
            $resource->status = $value;
            $resource->progress = $status['progress'][$id];

            if (strtolower($value) == 'closed') {
                $resource->progress = 100;
            } elseif ($resource->progress == 100) {
                $resource->status = 'Closed';
            }

            /** @var BreakDownResourceShadow $resource */
            $resource->save();

            $resource->actual_resources()
                ->orderBy('id', 'desc')->first()
                ->update(['progress' => $resource->progress, 'status' => $resource->status]);

//            $cost = BreakDownResourceShadow::where('breakdown_resource_id', $id)->first();
            $log = ['resource' => $resource, 'remaining_qty' => $resource->remaining_qty, 'to_date_qty' => $resource->to_date_qty];
            unset($resource->cost, $resource->imported_cost);
            $statusLog->push($log);
        }

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordStatus($statusLog);

        return ['success' => $resources->count(), 'batch' => $this->batch];
    }

    public function validatePhysicalQty($request)
    {
        $checker = new CostImporter($this->batch);
        $result = $checker->checkPhysicalQty();
        $errors = $result['errors'];

        $quantities = $request->input('quantities');

        $validationErrors = [];
        foreach ($quantities as $key => $quantity) {
            $total = $errors->get($key, collect())->get('rows', collect())->sum(6);
            $quantity = floatval($quantity);
            if ($quantity) {
                $unit_price = $total / $quantity;
                if ($unit_price < 0) {
                    $validationErrors["quantities.$key"] = 'Invalid quantity';
                }
            } else {
                $validationErrors["quantities.$key"] = 'Invalid quantity';
            }
        }

        return $validationErrors;
    }

}