<?php
namespace App\Support;

use App\ActualBatch;

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

    function recordActivityMappingUnPrivileged($data)
    {

    }

    function recordActivityMappingPrivileged($mapping)
    {

    }

    function recordResourceMappingUnPrivileged($activity)
    {

    }

    function recordPhysicalQuantity($resources, $correction)
    {

    }

    function recordClosedResources($resources, $correction)
    {

    }

    function recordProgress($resources)
    {

    }

    function recordStatus($resources)
    {

    }

    function recordInvalid($resources)
    {

    }

    function recordCostAccountDistribution($resources, $distribution)
    {

    }

    public function recordResourceMappingPrivileged($mapping)
    {
    }
}