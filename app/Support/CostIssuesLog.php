<?php
namespace App\Support;

use App\ActualBatch;
use App\CostIssue;

class CostIssuesLog
{
    /**
     * @var ActualBatch
     */
    private $batch;

    function __construct(ActualBatch $batch)
    {
        $this->batch = $batch;
    }

    function recordActivityMappingUnPrivileged($mapping)
    {
        $this->record('activity_mapping_unprivileged', $mapping);
    }

    function recordActivityMappingPrivileged($mapping)
    {
        $this->record('activity_mapping_privileged', $mapping);
    }

    function recordResourceMappingUnPrivileged($mapping)
    {
        $this->record('resource_mapping_unprivileged', $mapping);
    }

    public function recordResourceMappingPrivileged($mapping)
    {
        $this->record('resource_mapping_privileged', $mapping);
    }

    function recordPhysicalQuantity($resources)
    {
        $this->record('physical_qty', $resources);
    }

    function recordClosedResources($resources)
    {
        $this->record('closed_resources', $resources);
    }

    function recordProgress($resources)
    {
        $this->record('progress', $resources);
    }

    function recordStatus($resources)
    {
        $this->record('status', $resources);
    }

    function recordInvalid($resources)
    {
        $this->record('invalid', $resources);
    }

    function recordCostAccountDistribution($resources)
    {
        $this->record('account_distribution', $resources);
    }

    protected function record($type, $data)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        CostIssue::create([
            'batch_id' => $this->batch->id,
            'type' => $type,
            'data' => $data,
        ]);
    }

}