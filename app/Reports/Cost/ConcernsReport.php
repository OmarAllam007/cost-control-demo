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
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/cost-summary-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);

        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_issues_concerns.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $this->run();
    }
}