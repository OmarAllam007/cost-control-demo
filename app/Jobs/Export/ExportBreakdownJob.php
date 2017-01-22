<?php

namespace App\Jobs\Export;

use App\Jobs\Job;

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
        ini_set('memory_limit', '1G');

        $headers = [
            'WBS-Level-1', 'WBS-Level-2', 'WBS-Level-3', 'WBS-Level-4','WBS-Level-5','WBS-Level-6','WBS-Level-7','Activity-Division-1','Activity-Division-2','Activity-Division-3',
            'Activity-Division-4','Activity ID',
            'Activity','Discipline',
            'Breakdown-Template', 'Cost Account',
            'Engineering Quantity', 'BudgetQuantity','Resource Quantity', 'Resource Waste', 'Resource Type', 'Resource Code', 'Resource Name', 'Price - Unit', 'Unit Of Measure', 'Budget Unit', 'Budget Cost',
            'BOQ Equivalent Unit Rate','No. Of Labors', 'Productivity (Unit/Day)', 'Productivity Reference', 'Remarks'
        ];

        $lines = [
            implode(",", array_map([$this, 'csv_quote'], $headers))
        ];

        $shadows = $this->project->shadows->load('std_activity', 'std_activity.division.parent.parent.parent', 'wbs', 'wbs.parent.parent.parent');
        foreach ($shadows as $breakdown_resource) {
            $discpline = $breakdown_resource->std_activity->discipline;
            $division = $breakdown_resource->std_activity->division;
            $level = $breakdown_resource->wbs;
            $levels = [];
            $divisions=[];
            $levels[] = $level->name;

            $parent = $level->parent;
            while ($parent) {
                $levels[] = $parent->name;
                $parent = $parent->parent;
            };
            $levels = array_reverse($levels);

            $divisions[]=$division->name;

            $parentDiv = $division->parent;
            while($parentDiv){
                $divisions[] = $parentDiv->name;
                $parentDiv= $parentDiv->parent;
            }
            $divisions = array_reverse($divisions);
            $data = [
                isset($levels[0]) ? $levels[0] : '',
                isset($levels[1]) ? $levels[1] : '',
                isset($levels[2]) ? $levels[2] : '',
                isset($levels[3]) ? $levels[3] : '',
                isset($levels[4]) ? $levels[4] : '',
                isset($levels[5]) ? $levels[5] : '',
                isset($levels[6]) ? $levels[6] : '',
                isset($divisions[0]) ? $divisions[0] : '',
                isset($divisions[1]) ? $divisions[1] : '',
                isset($divisions[2]) ? $divisions[2] : '',
                isset($divisions[3]) ? $divisions[3] : '',
                $breakdown_resource['code'],
                $breakdown_resource['activity'],
                $discpline,
                $breakdown_resource['template'],
                $breakdown_resource['cost_account'],
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

            $lines[] = implode(",", array_map([$this, 'csv_quote'], $data));

            unset($levels, $level, $division, $divisions, $discpline);
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - BreakDown.csv"');
        unset($shadows);
//        header('Cache-Control: max-age=0');

        file_put_contents('php://output', implode(PHP_EOL, $lines));

    }

    protected function csv_quote($str)
    {
        return '"' . str_replace('"', '""', preg_replace('/\s+/', ' ', $str)) . '"';
    }
}
