<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 09/01/17
 * Time: 09:23 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use App\StdActivity;
use App\Survey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class VarianceAnalysisReport
{

    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $project = $this->project;
        $tree = $this->buildTypeTree();
        return view('reports.cost-control.variance_analysis.variance_analysis', compact('tree', 'project'));
    }

    function buildTypeTree()
    {
        $query = MasterShadow::forPeriod($this->period)->varAnalysisReport();

        $resourceData = $this->applyFilters($query)->get();

        $tree = $resourceData->groupBy('resource_type')->map(function($typeGroup) {
            return $typeGroup->groupBy('boq_discipline');
        });

        return $tree;
    }

    protected function applyFilters(Builder $query)
    {
        return $query;
    }
}