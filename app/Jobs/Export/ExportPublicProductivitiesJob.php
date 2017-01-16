<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Productivity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportPublicProductivitiesJob extends Job
{


    public function handle()
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'CSI Category', 'Daily Output', 'Reduction Factor', 'Description'
            , 'Crew Structure', 'Unit', 'Source'], 'A1');

        $rowCount = 2;
        $productivities = Productivity::all();
        foreach ($productivities as $productivity) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $productivity->csi_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $productivity->category->name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $productivity->daily_output);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $productivity->reduction_factor);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $productivity->description);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $productivity->crew_structure);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $productivity->units->type);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $productivity->source);
            $rowCount++;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="All-Productivities.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
