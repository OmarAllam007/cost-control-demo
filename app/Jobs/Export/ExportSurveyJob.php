<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Unit;


class ExportSurveyJob extends Job
{
    public $project;
    public function __construct($project)
    {
        $this->project = $project;
    }


    public function handle()
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Cost Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'WBS-Level');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Budget Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Engineer Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Unit');

        $rowCount = 2;
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
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, Unit::find($quantity->unit_id)->type);
            $rowCount++;

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Quantity Survey.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
