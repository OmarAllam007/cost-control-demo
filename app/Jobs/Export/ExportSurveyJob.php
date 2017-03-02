<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Survey;
use App\Unit;
use App\WbsLevel;


class ExportSurveyJob extends Job
{
    public $project;
    private $units;
    private $wbs_levels;
    private $variables;
    public function __construct($project)
    {
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id',$project->id)->get()->keyBy('id')->map(function ($level){
            return $level;
        });

        $this->variables  = Survey::with('variables')->where('project_id',$project->id)->get()->keyBy('id')->map(function ($survey){
           return $survey->variables;
        });

        $this->units = Unit::all()->keyBy('id')->map(function ($unit){
            return $unit->type;
        });
    }


    public function handle()
    {
        set_time_limit(600);
        $project = $this->project;


        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $col = 0;
        $rowCount = 2;
        $variable_number = 1;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'Cost Account');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'WBS-Levels');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'WBS-Code');
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
            $wbs_levels = $this->wbs_levels->get($quantity->wbs_level_id)->path;
            $wbs_code = $this->wbs_levels->get($quantity->wbs_level_id)->code;
            $description = $quantity->description;
            $budget_qty = $quantity->budget_qty;
            $eng_qty = $quantity->eng_qty;
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $cost_account);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $wbs_levels);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $wbs_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $description);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $budget_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $eng_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $this->units->get($quantity->unit_id));
            $col++;
            if($this->variables->get($quantity->id)->count()){
                foreach ($this->variables->get($quantity->id) as $variable){
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1,'$V-'.$variable_number);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,$variable->name.' = '.$variable->value);
                    $variable_number++;
                    $col++;
                }

            }
            $variable_number=1;
            $col = 6;
            $rowCount++;
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$project->name.'- Survey.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
