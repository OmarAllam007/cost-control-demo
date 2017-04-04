<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Resources;
use App\ResourceType;

class ResourceCodeReport
{
    /** @var Project */
    protected $project;
    /** @var Period */
    protected $period;

    function __construct(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->period = Period::find($chosen_period_id);
    }

    public function run()
    {
        $tree = $this->buildTree();
        $project = $this->project;
        return view('reports.cost-control.resource_code.resource_code', compact('project', 'tree'));
    }

    private function buildTree()
    {
        $tree = [];

        $resourcetData = MasterShadow::forPeriod($this->period)->resourceDictReport()->get();

        $tree = $resourcetData->groupBy('resource_type')->map(function($typeGroup) {
            return $typeGroup->groupBy('boq_discipline')->map(function($disciplineGroup) {
                return $disciplineGroup->groupBy('top_material');
            });
        });

        return $tree;
    }

}