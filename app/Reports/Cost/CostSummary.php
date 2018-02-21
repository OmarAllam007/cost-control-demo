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
        $resourceTypes = [1 => 'Indirect', 2 => 'Direct'];
        $previousPeriod = $this->project->periods()->where('id', '<', $this->period->id)->latest()->first();
        if ($previousPeriod) {
            $prevData = MasterShadow::where('period_id', '=', $previousPeriod->id)
                ->selectRaw('resource_type_id, sum(to_date_cost) as previous_cost, sum(allowable_ev_cost) as previous_allowable, sum(allowable_var) as previous_var')
                ->groupBy('resource_type_id')->get()->keyBy('resource_type_id');
            $previousData = $this->prevDate($prevData);

        } else {
            $previousData = $this->prevDate();
        }

        $fields = [
            'resource_type_id', 'sum(budget_cost) budget_cost', 'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as ev',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost', 'sum(completion_cost) as completion_cost',
            'sum(cost_var) as completion_cost_var'
        ];

        $toDate = MasterShadow::where('period_id', $this->period->id)->selectRaw(implode(', ', $fields))
            ->groupBy('resource_type_id')->get()->keyBy('resource_type_id');

        $project = $this->project;

        $toDateData = $this->toDate($toDate);

        foreach ($toDateData as $key => $value) {
            foreach ($value as $id => $data) {
                $this->total[$id] += $data + isset($previousData[$key]) ? $previousData[$key][$id] : 0;
            }
        }
        dd($this->total);

        return compact('previousData', 'toDateData', 'project', 'resourceTypes');
    }

    function toDate($toDateData)
    {

        $toDate = [
            'indirect' => [
                'budget_cost' => 0, 'to_date_cost' => 0, 'ev' => 0, 'to_date_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0, 'completion_cost_var' => 0
            ],
            'direct' => [
                'budget_cost' => 0, 'to_date_cost' => 0, 'ev' => 0, 'to_date_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0, 'completion_cost_var' => 0
            ],
        ];
        $toDate['indirect']['budget_cost'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('budget_cost');
        $toDate['indirect']['to_date_cost'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('to_date_cost');
        $toDate['indirect']['ev'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('ev');
        $toDate['indirect']['to_date_var'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('to_date_var');
        $toDate['indirect']['remaining_cost'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('remaining_cost');
        $toDate['indirect']['completion_cost'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('completion_cost');
        $toDate['indirect']['completion_cost_var'] = $toDateData->whereIn('resource_type_id', [1, 8])->sum('completion_cost_var');

        $toDate['direct']['budget_cost'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('budget_cost');
        $toDate['direct']['to_date_cost'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('to_date_cost');
        $toDate['direct']['ev'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('ev');
        $toDate['direct']['to_date_var'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('to_date_var');
        $toDate['direct']['remaining_cost'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('remaining_cost');
        $toDate['direct']['completion_cost'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('completion_cost');
        $toDate['direct']['completion_cost_var'] = $toDateData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('completion_cost_var');
        return $toDate;
    }

    function prevDate($prevData)
    {
        $prevDate = [
            'indirect' => [
                'budget_cost' => 0, 'to_date_cost' => 0, 'ev' => 0, 'to_date_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0, 'completion_cost_var' => 0
            ],
            'direct' => [
                'budget_cost' => 0, 'to_date_cost' => 0, 'ev' => 0, 'to_date_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0, 'completion_cost_var' => 0
            ],
        ];
        if(count($prevData)){
            $prevDate['indirect']['budget_cost'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('budget_cost');
            $prevDate['indirect']['to_date_cost'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('to_date_cost');
            $prevDate['indirect']['ev'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('ev');
            $prevDate['indirect']['to_date_var'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('to_date_var');
            $prevDate['indirect']['remaining_cost'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('remaining_cost');
            $prevDate['indirect']['completion_cost'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('completion_cost');
            $prevDate['indirect']['completion_cost_var'] = $prevData->whereIn('resource_type_id', [1, 8])->sum('completion_cost_var');

            $prevDate['direct']['budget_cost'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('budget_cost');
            $prevDate['direct']['to_date_cost'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('to_date_cost');
            $prevDate['direct']['ev'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('ev');
            $prevDate['direct']['to_date_var'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('to_date_var');
            $prevDate['direct']['remaining_cost'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('remaining_cost');
            $prevDate['direct']['completion_cost'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('completion_cost');
            $prevDate['direct']['completion_cost_var'] = $prevData->whereIn('resource_type_id', [2, 3, 4, 5, 6, 7])->sum('completion_cost_var');

        }

        return $prevData;
    }


}