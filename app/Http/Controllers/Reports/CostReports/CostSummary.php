<?php

namespace App\Http\Controllers\Reports\CostReports;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostConcerns;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

class CostSummary
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Period
     */
    protected $period;

    function __construct(Period $period)
    {

        $this->project = $period->project;
        $this->period = $period;
    }

    function run()
    {
        $resourceTypes = ResourceType::where('parent_id', 0)->orderBy('name')->pluck('name', 'id');

        $previousPeriod = $this->project->periods()->where('id', '<', $this->period->id)->latest()->first();
        if ($previousPeriod) {
            $previousData = MasterShadow::where('period_id', '=', $previousPeriod->id)
                ->selectRaw('resource_type_id, sum(to_date_cost) as previous_cost, sum(allowable_ev_cost) as previous_allowable, sum(allowable_var) as previous_var')
                ->groupBy('resource_type_id')->get()->keyBy('resource_type_id');
        } else {
            $previousData = collect();
        }

        $fields = [
            'resource_type_id', 'sum(budget_cost) budget_cost', 'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as ev',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost', 'sum(completion_cost) as completion_cost',
            'sum(cost_var) as completion_cost_var'
        ];

        $toDateData = MasterShadow::where('period_id', $this->period->id)->selectRaw(implode(', ', $fields))->groupBy('resource_type_id')->get()->keyBy('resource_type_id');
        $project = $this->project;

        return compact('previousData', 'toDateData', 'project', 'resourceTypes');
    }

    function excel()
    {
        \Excel::load(storage_path('templates/cost-summary.xlsx'), function (LaravelExcelReader $reader) {
            $activeSheet = $reader->getSheet(0);
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $data = $this->run();
    }
}