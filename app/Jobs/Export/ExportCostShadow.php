<?php

namespace App\Jobs\Export;

use App\Jobs\Job;

class ExportCostShadow extends Job
{

    protected $project;

    public function __construct($project)
    {
        $this->project = $project;
    }


    public function handle()
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->fromArray([
            'WBS-Level-1',
            'WBS-Level-2',
            'WBS-Level-3',
            'WBS-Level-4',
            'Activity Name',
            'Breakdown Template',
            'Cost Account',
            'Eng Quantity',
            'Budget Quantity',
            'Resource Quantity',
            'Resource Waste',
            'Resource Type',
            'Resource Code',
            'Resource Name',
            'Price/Unit',
            'Unit Of Measure',
            'Budget Unit',
            'Budget Cost',
            'BOQ Equivalent Unit rate',
            'No. Of Labors',
            'Productivity (Unit/Day)',
            'Productivity Ref',
            'Remarks',
            'Progress',
            'Status',
            'Prev. Price/Unit',
            'Prev. Quantity',
            'Prev. Cost',
            'Current. Price/Unit',
            'Current Quantity',
            'Current Cost',
            'To Date Price/Unit(Eqv)',
            'To Date Quantity',
            'To Date Cost',
            'Allowable (EV) cost',
            'Var +/-',
            'Remaining Price/Unit',
            'Remaining Qty',
            'Remaining Cost',
            'BL Allowable Cost',
            'Var +/- 10',
            'Completion Price/Unit',
            'Completion Qty',
            'Completion Cost',
            'Price/Unit Var',
            'Qty Var +/-',
            'Cost Var +/-',
            'Physical Unit',
            '(P/W) Index',
            'Cost Variance To Date Due to Unit Price',
            'Allowable Quantity',
            'Cost Variance Remaining Due to Unit Price',
            'Cost Variance Completion Due to Unit Price',
            'Cost Variance Completion Due to Qty',
            'Cost Variance to Date Due to Qty',
        ], 'A1');
        $rowCount = 2;
        $columnCount = 1;
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $sheet->getDefaultStyle()->applyFromArray($style);
        foreach ($this->project->cost_shadow as $costShadow) {
            $budget = $costShadow->budget;
            $levels = [];
            $parent = $budget->wbs;
            $levels[] = $parent->name;
            $parent = $parent->parent;

            while ($parent) {
                $levels[] = $parent->name;
                $parent = $parent->parent;
            };
            $levels = array_reverse($levels);

            $sheet->getStyle('A1:BC1')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'CCFFFF')
                    )
                )
            );
            $this->styleColumns($sheet, 'A' . $rowCount . ':' . 'W' . $rowCount, 'E0E0E0');
            $this->styleColumns($sheet, 'X' . $rowCount . ':' . 'Y' . $rowCount, 'CCCCFF');
            $this->styleColumns($sheet, 'Z' . $rowCount . ':' . 'AB' . $rowCount, 'CCFFCC');
            $this->styleColumns($sheet, 'AC' . $rowCount . ':' . 'AJ' . $rowCount, 'FFCC99');
            $this->styleColumns($sheet, 'AK' . $rowCount . ':' . 'AR' . $rowCount, 'CCCCFF');
            $this->styleColumns($sheet, 'AS' . $rowCount . ':' . 'BC' . $rowCount, 'FFCC99');
            $columnCount++;
            $sheet->fromArray([
                isset($levels[0]) ? $levels[0] : '',
                isset($levels[1]) ? $levels[1] : '',
                isset($levels[2]) ? $levels[2] : '',
                isset($levels[3]) ? $levels[3] : '',
                $budget['activity'],
                $budget['template'],
                $budget['cost_account'],
                $budget['eng_qty'] ?: '0',
                $budget['budget_qty'] ?: '0',
                $budget['resource_qty'] ?: '0',
                $budget['resource_waste'] ?: '0',
                $budget['resource_type'],
                $budget['resource_code'],
                $budget['resource_name'],
                $budget['unit_price'] ?: '0',
                $budget['measure_unit'],
                $budget['budget_unit'] ?: '0',
                $budget['budget_cost'] ?: '0',
                $budget['boq_equivilant_rate'] ?: '0',
                $budget['labors_count'],
                $budget['productivity_output'] ?: '0',
                $budget['productivity_ref'] ?: '0',
                $budget['remarks'],
                $budget->progress,
                $budget->status,
                $costShadow['previous_unit_price'] ?: '0',
                $costShadow['previous_qty'] ?: '0',
                $costShadow['previous_cost'] ?: '0',
                $costShadow['current_unit_price'] ?: '0',
                $costShadow['current_qty'] ?: '0',
                $costShadow['current_cost'] ?: '0',
                $costShadow['to_date_unit_price'] ?: '0',
                $costShadow['to_date_qty'] ?: '0',
                $costShadow['to_date_cost'] ?: '0',
                $costShadow['allowable_ev_cost'] ?: '0',
                $costShadow['allowable_var'] ?: '0',
                $costShadow['remaining_unit_price'] ?: '0',
                $costShadow['remaining_qty'] ?: '0',
                $costShadow['remaining_cost'] ?: '0',
                $costShadow['bl_allowable_cost'] ?: '0',
                $costShadow['bl_allowable_var'] ?: '0',
                $costShadow['completion_unit_price'] ?: '0',
                $costShadow['completion_qty'] ?: '0',
                $costShadow['completion_cost'] ?: '0',
                $costShadow['unit_price_var'] ?: '0',
                $costShadow['qty_var'] ?: '0',
                $costShadow['cost_var'] ?: '0',
                $costShadow['physical_unit'] ?: '0',
                $costShadow['pw_index'] ?: '0',
                $costShadow['cost_variance_to_date_due_unit_price'] ?: '0',
                $costShadow['allowable_qty'] ?: '0',
                $costShadow['cost_variance_remaining_due_unit_price'] ?: '0',
                $costShadow['cost_variance_completion_due_unit_price'] ?: '0',
                $costShadow['cost_variance_completion_due_qty'] ?: '0',
                $costShadow['cost_variance_to_date_due_qty'] ?: '0'

            ] ?: '0', Null, 'A' . $rowCount);
            $rowCount++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - DataSheet.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    function styleColumns($sheet, $range, $color)
    {
        $sheet->getStyle("" . $range . "")->applyFromArray(
            array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => $color)
                )
            ));
    }
}
