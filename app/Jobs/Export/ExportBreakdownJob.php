<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use phpDocumentor\Reflection\Types\Null_;

class ExportBreakdownJob extends Job
{


    public $project;

    public function __construct($project)
    {
        $this->project = $project;

    }

    public function handle()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->fromArray(['WBS-Level-1', 'WBS-Level-2', 'WBS-Level-3', 'WBS-Level-4', 'Activity', 'Breakdown-Template', 'Cost Account', 'Engineering Quantity', 'Budget Quantity', 'Resource Quantity', 'Resource Waste', 'Resource Type', 'Resource Code', 'Resource Name', 'Price - Unit', 'Unit Of Measure', 'Budget Unit', 'Budget Cost', 'BOQ Equivalent Unit Rate', 'No. Of Labors', 'Productivity (Unit/Day)', 'Productivity Reference', 'Remarks'], 'A1');
        $rowCount = 2;

        $sheet->getStyle('A1:W1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E5FFCC')
                )
            )
        );

        foreach ($this->project->shadows as $breakdown_resource) {
            $parent = $breakdown_resource->wbs;
            $levels = [];
            $levels[] = $parent->name;
            $parent = $parent->parent;

            while ($parent) {
                $levels[] = $parent->name;
                $parent = $parent->parent;
            };

            $levels = array_reverse($levels);

            $sheet->fromArray([
                isset($levels[0]) ? $levels[0] : '',
                isset($levels[1]) ? $levels[1] : '',
                isset($levels[2]) ? $levels[2] : '',
                isset($levels[3]) ? $levels[3] : '',
                $breakdown_resource['activity'],
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
            ], Null, 'A' . $rowCount); $rowCount++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - BreakDown.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');

    }
}