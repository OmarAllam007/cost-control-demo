<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/15/17
 * Time: 2:36 PM
 */

namespace App\Reports\Cost;


use App\MasterShadow;
use App\Period;

class ProjectInfo
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
        $summary = new CostSummary($this->period);
        $this->costSummary = $summary->run();
        $this->cpiTrend = MasterShadow::where('master_shadows.project_id', $this->project->id)->cpiTrendChart()->get();

        return [
            'project' => $this->project,
            'costSummary' => $this->costSummary,
            'period' => $this->period,
            'cpiTrend' => $this->cpiTrend
        ];
    }

    function excel()
    {

    }
}