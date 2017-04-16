<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 03:45 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;

class ProjectInformation
{
    /**
     * @var Period
     */
    private $period;

    function __construct(Period $period)
    {
        $this->period = $period;
    }

    function run()
    {
        $data = MasterShadow::forPeriod($this->period)
            ->selectRaw('sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost, sum(allowable_var) as cost_var')
            ->first()->toArray();

        if ($data['to_date_cost']) {
            $data['cpi'] = $data['allowable_cost'] / $data['to_date_cost'];
        } else {
            $data['cpi'] = 0;
        }

        $project = $this->period->project;

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        return view('reports.cost-control.project_information', compact('project', 'data', 'periods'));
    }

}