<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/11/17
 * Time: 3:42 PM
 */

namespace App\Support\Import;


use App\BreakDownResourceShadow;
use App\CostManDay;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;

class CostManDaysExport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Collection */
    protected $activities;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;

        $this->loadActivities();
    }

    function export()
    {
        $excel = new \PHPExcel();
        $counter = 1;
        $sheet = $excel->getSheet(0);

        $sheet->fromArray(['Activity Code', 'Actual Man Days', 'Progress'], null, "A1", true);

        $manDays = CostManDay::where('period_id', $this->period->id)->get();
        foreach ($manDays as $day) {
            ++$counter;
            $key = "{$day->wbs_id}.{$day->activity_id}";
            $code = $this->activities->get($key);

            $sheet->fromArray([$code, $day->actual, $day->progress], null, "A{$counter}", true);
        }

        $sheet->getStyle("B2:B$counter")->getNumberFormat()->setBuiltInFormatCode(38);
        $sheet->getStyle("C2:C$counter")->getNumberFormat()->setBuiltInFormatCode(10);

        $filename = storage_path('app/' . slug($this->project->name) . '_' . slug($this->period->name) . '_man_days.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return $filename;
    }

    protected function loadActivities()
    {
        $this->activities = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('code, wbs_id, activity_id')
            ->get()
            ->keyBy(function ($resource) {
                return "{$resource->wbs_id}.{$resource->activity_id}";
            })->map(function ($resource) {
                return $resource->code;
            });
    }
}