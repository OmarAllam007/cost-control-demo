<?php

namespace App\Jobs\Export;

use App\BoqDivision;
use App\Jobs\Job;
use App\Unit;
use App\WbsLevel;

class ExportBoqJob extends Job
{

    public $project ;
    public function __construct($project)
    {
        $this->project = $project;
    }

    public function handle()
    {
        set_time_limit(600);
        $project = $this->project;
        $divisions = BoqDivision::whereHas('items', function ($q) use ($project) {
            $q->where('project_id', $this->project->id);
        })->get();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Cost Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Discipline');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Unit');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Estimated Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Unit Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Unit Dry');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'KCC-Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Materials');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SubContractors');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Man Power');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'WBS-LEVEL');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Division');
        $rowCount = 2;

        foreach ($divisions as $division) {
            if ($division->items->count()) {
                foreach ($division->items as $item) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->item_code);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->cost_account);

                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $item->description);

                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $item->type);

                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, isset(Unit::find($item->unit_id)->type) ? Unit::find($item->unit_id)->type : '');

                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $item->quantity);

                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $item->price_ur);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $item->dry_ur);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $item->kcc_qty);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $item->materials);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $item->subcon);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $item->subcon);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, WbsLevel::find($item->wbs_id)->path);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, BoqDivision::find($item->division_id)->name);
                    $rowCount++;
                }
            }

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - BOQ.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
