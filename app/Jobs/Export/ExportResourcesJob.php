<?php

namespace App\Jobs\Export;

use App\BusinessPartner;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportResourcesJob extends Job
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
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Resource Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Rate');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Unit');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Waste');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'reference');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Business Partner');
        $rowCount = 2;
        foreach ($this->project->plain_resources as $resource) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource->resource_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource->name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource->types->root->name);

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource->versionFor($this->project->id)->rate);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, isset($resource->versionFor($this->project->id)->units->type) ? $resource->versionFor($this->project->id)->units->type : '');

            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $resource->versionFor($this->project->id)->waste . '%');

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $resource->reference);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, isset(BusinessPartner::find($resource->business_partner_id)->name) ? BusinessPartner::find($resource->business_partner_id)->name : '');
            $rowCount++;

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
