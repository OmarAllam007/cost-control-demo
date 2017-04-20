<?php

namespace App\Jobs\Export;

use App\Boq;
use App\Jobs\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExportBreakdownJob extends Job
{


    public $project;

    public function __construct($project)
    {
        $this->project = $project;

    }

    public function handle()
    {
        set_time_limit(600);
        $filename = storage_path('app/' . uniqid('breakdown_csv_'));

        $headers = [
            'APP_ID', 'WBS-Level-1', 'WBS-Level-2', 'WBS-Level-3', 'WBS-Level-4', 'WBS-Level-5', 'WBS-Level-6', 'WBS-Level-7', 'WBS Path', 'Activity-Division-1', 'Activity-Division-2', 'Activity-Division-3',
            'Activity-Division-4', 'Activity ID',
            'Activity', 'Discipline',
            'Breakdown-Template', 'Cost Account', 'BOQ item Description',
            'Engineering Quantity', 'BudgetQuantity', 'Resource Quantity', 'Resource Waste', 'Resource Type', 'Resource Code', 'Resource Name', 'Price - Unit', 'Unit Of Measure', 'Budget Unit', 'Budget Cost',
            'BOQ Equivalent Unit Rate', 'No. Of Labors', 'Productivity (Unit/Day)', 'Productivity Reference', 'Remarks'
        ];

        $line = implode(",", array_map([$this, 'csv_quote'], $headers));
        $fh = fopen($filename, 'w');
        fwrite($fh, $line);

        /** @var LengthAwarePaginator $shadows */
        $shadows = $this->project->shadows()->with('std_activity', 'std_activity.division.parent.parent.parent', 'wbs', 'wbs.parent.parent.parent')->chunk(20000, function ($shadows) use ($fh) {
            foreach ($shadows as $breakdown_resource) {
                $discpline = $breakdown_resource->std_activity->discipline;
                $division = $breakdown_resource->std_activity->division;
                $level = $breakdown_resource->wbs;
                $levels = [];
                $divisions = [];
                $levels[] = $level->name;

                $parent = $level->parent;
                while ($parent) {
                    $levels[] = $parent->name;
                    $parent = $parent->parent;
                };
                $levels = array_reverse($levels);

                $divisions[] = $division->name;

                $parentDiv = $division->parent;
                while ($parentDiv) {
                    $divisions[] = $parentDiv->name;
                    $parentDiv = $parentDiv->parent;
                }
                $divisions = array_reverse($divisions);
                $boq = Boq::costAccountOnWbs($breakdown_resource->wbs, $breakdown_resource->cost_account)->first();
                $data = [
                    $breakdown_resource->id,
                    isset($levels[0]) ? $levels[0] : '',
                    isset($levels[1]) ? $levels[1] : '',
                    isset($levels[2]) ? $levels[2] : '',
                    isset($levels[3]) ? $levels[3] : '',
                    isset($levels[4]) ? $levels[4] : '',
                    isset($levels[5]) ? $levels[5] : '',
                    isset($levels[6]) ? $levels[6] : '',
                    $level->code,
                    isset($divisions[0]) ? $divisions[0] : '',
                    isset($divisions[1]) ? $divisions[1] : '',
                    isset($divisions[2]) ? $divisions[2] : '',
                    isset($divisions[3]) ? $divisions[3] : '',
                    $breakdown_resource['code'],
                    $breakdown_resource['activity'],
                    $discpline,
                    $breakdown_resource['template'],
                    $breakdown_resource['cost_account'],
                    $boq? $boq->description : '',
                    $breakdown_resource['eng_qty'],
                    $breakdown_resource['budget_qty'],
                    $breakdown_resource['resource_qty'],
                    $breakdown_resource['resource_waste'],
                    $breakdown_resource['resource_type'],
                    $breakdown_resource['resource_code'],
                    $breakdown_resource['resource_name'],
                    $breakdown_resource['unit_price'],
                    $breakdown_resource['measure_unit'],
                    $breakdown_resource['budget_unit'],
                    $breakdown_resource['budget_cost'],
                    $breakdown_resource['boq_equivilant_rate'],
                    $breakdown_resource['labors_count'],
                    $breakdown_resource['productivity_output'],
                    $breakdown_resource['productivity_ref'],
                    $breakdown_resource['remarks'],
                ];

                $line = PHP_EOL . implode(",", array_map([$this, 'csv_quote'], $data));
                fwrite($fh, $line);

                unset($levels, $level, $division, $divisions, $discpline, $line);
            }
        });

//
//        header('Content-Type: text/csv');
//        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - BreakDown.csv"');
        unset($shadows);
//        header('Cache-Control: max-age=0');
//        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
//        $objWriter->save('php://output');

        fclose($fh);

        return $filename;
    }

    protected function csv_quote($str)
    {
        return '"' . str_replace('"', '""', preg_replace('/\s+/', ' ', $str)) . '"';
    }
}
