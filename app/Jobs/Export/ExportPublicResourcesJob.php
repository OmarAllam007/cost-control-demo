<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Resources;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportPublicResourcesJob extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'Resource Name', 'Type', 'Rate', 'Unit'
            , 'Waste', 'reference', 'Business Partner'], 'A1');



        $rowCount = 2;
        $resources = Resources::whereNull('project_id')->get();
        foreach ($resources as $resource) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource->resource_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource->name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource->types->root->name ?? '');

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource->rate);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $resource->units->type??'');

            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $resource->waste);

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $resource->reference);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  isset($resource->parteners->name)?$resource->parteners->name:'');
            $rowCount++;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="All-Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
