<?php

namespace App\Jobs\Export;

use App\Jobs\Job;


class ExportProductivityJob extends Job
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
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Category Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Crew Structure');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Daily Output');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'After Reduction');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Reduction Factor');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Unit');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Source');
        $rowCount = 2;
        foreach ($this->project->productivities as $productivity) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $productivity->csi_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $productivity->category->path);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $productivity->description);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $productivity->crew_structure);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $productivity->versionFor($this->project->id)->daily_output);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $productivity->versionFor($this->project->id)->after_reduction);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $productivity->reduction_factor);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $productivity->units->type);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $productivity->source);
            $rowCount++;
        }



        header('Content-Disposition: attachment; filename="' . $this->project->name . ' - Productivity.xls"');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
