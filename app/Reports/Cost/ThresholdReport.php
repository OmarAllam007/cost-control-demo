<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/23/17
 * Time: 4:41 PM
 */

namespace App\Reports\Cost;


use App\Period;
use App\Project;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

class ThresholdReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->threshold = $this->project->cost_threshold;
    }

    function run()
    {

    }

    protected function buildTree()
    {

    }

    function excel()
    {

    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();
    }
}