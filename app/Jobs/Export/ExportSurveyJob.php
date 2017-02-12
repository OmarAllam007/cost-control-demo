<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Unit;


class ExportSurveyJob extends Job
{
    public $project;
    private $units;
    public function __construct($project)
    {
        $this->project = $project;
    }


    public function handle()
    {
        set_time_limit(600);
        $this->units = Unit::all()->keyBy('id')->map(function ($unit){
            return $unit->type;
        });
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Cost Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'WBS-Levels');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Budget Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Engineer Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Unit');
        $col = 0;
        $rowCount = 2;
        $variable_number = 1;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Cost Account');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'WBS-Levels');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Description');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Budget Quantity');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Engineer Quantity');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Unit');
        $col++;

        foreach ($this->project->quantities as $quantity) {
            $cost_account = $quantity->cost_account;
            $wbs_level = $quantity->wbsLevel->path;
            $description = $quantity->description;
            $budget_qty = $quantity->budget_qty;
            $eng_qty = $quantity->eng_qty;

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $cost_account);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $wbs_level);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $description);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $budget_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $eng_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $this->units->get($quantity->unit_id));
            if($quantity->variables->count()){
                foreach ($quantity->variables as $variable){
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'$V-'.$variable_number);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,$variable->name);
                    $variable_number++;
                    $col++;
                }

            }
            $variable_number=1;
            $col = 6;
            $rowCount++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Quantity Survey.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
