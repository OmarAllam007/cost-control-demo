<?php

namespace App\Jobs\Export;

use App\Boq;
use App\BoqDivision;
use App\Jobs\Job;
use App\Unit;
use App\WbsLevel;

class ExportBoqJob extends Job
{

    public $project;
    private $wbs_levels;
    private $boqs;
    private $units;

    public function __construct($project)
    {
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id',$project->id)->get()->keyBy('id')->map(function ($level){
            return $level;
        });
        $this->boqs = BoqDivision::all()->keyBy('id')->map(function ($division){
           return $division->name;
        });
        $this->units = Unit::all()->keyBy('id')->map(function ($unit){
            return $unit->type;
        });


    }

    public function handle()
    {
        set_time_limit(600);
        $project = $this->project;
        $items = Boq::where('project_id', $this->project->id)->get();

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
//        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Division');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'WBS_PATH');
        $rowCount = 2;
        foreach ($items as $item) {
            $code = $item->wbs->code;
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->item_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->cost_account);

            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $item->description);

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $item->type);

            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $this->units->get($item->unit_id));

            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $item->quantity);

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $item->price_ur);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $item->dry_ur);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $item->kcc_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $item->materials);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $item->subcon);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $item->subcon);

            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $this->wbs_levels->get($item->wbs_id)->path);
//            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $this->boqs->get($item->division_id));
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $code);
            $rowCount++;
        }

//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="' ."');
//        header('Cache-Control: max-age=0');
//        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
//        $objWriter->save('php://output');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$project->name.'- BOQ.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }
}
