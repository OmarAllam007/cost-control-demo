<?php

namespace App\Reports\Cost;

use App\CostConcern;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;

class ConcernsReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var Collection */
    private $concerns;

    public function __construct($period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->concerns = CostConcern::where('period_id', $this->period->id)->get()->groupBy('report_name');
        $periods = $this->project->periods()->readyForReporting()->latest('end_date')->pluck('name', 'id');

        return ['concerns' => $this->concerns, 'project' => $this->project, 'period' => $this->period, 'periods' => $periods];
    }

    function excel()
    {

    }

    function sheet()
    {
        $this->run();
    }
}