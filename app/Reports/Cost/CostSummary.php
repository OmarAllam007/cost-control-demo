<?php

namespace App\Reports\Cost;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostConcerns;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;

class CostSummary
{
    /**
     * @var Project
     */
    protected $project;
    /**
     * @var Period
     */


    protected $total = [
        'budget_cost' => 0, 'to_date_cost' => 0, 'ev' => 0, 'to_date_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0, 'completion_cost_var' => 0
    ];
    protected $period;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {

        $time = microtime(1);
        $project = $this->project;

        $resourceTypes = [1 => 'Indirect', 2 => 'Direct'];

        $previousPeriod = $this->project->periods()->where('id', '<', $this->period->id)->latest()->first();
        if ($previousPeriod) {
            $previousData = MasterShadow::where('period_id', '=', $previousPeriod->id)
                ->selectRaw(" (CASE WHEN resource_type_id IN (1,8) THEN 'Indirect' ELSE 'Direct' END) AS 'Type', sum(to_date_cost) as previous_cost, sum(allowable_ev_cost) as previous_allowable, sum(allowable_var) as previous_var")
                ->groupBy('Type')->get()->keyBy('Type');
            $previousData = $this->prevDate($previousData);

          } else {
            $previousData = collect();
        }

        $fields = [
            "(CASE WHEN resource_type_id IN (1,8) THEN 'Indirect' ELSE 'Direct' END) AS 'Type'", 'sum(budget_cost) budget_cost', 'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as ev',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost', 'sum(completion_cost) as completion_cost',
            'sum(cost_var) as completion_cost_var'
        ];


        $toDateData = MasterShadow::where('period_id', $this->period->id)->selectRaw(implode(', ', $fields))
            ->groupBy('Type')->get()->keyBy('Type');

        return compact('previousData', 'toDateData', 'project', 'resourceTypes');
    }

}