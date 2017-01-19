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
    public $project_name;
    public $objPHPExcel;

    public function __construct($project)
    {
        $this->project = $project;
        $this->project_name = $this->project->name;

    }

    public function handle()
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'Resource Name', 'Type', 'Rate', 'Unit'
            , 'Waste', 'reference', 'Business Partner', 'Project Name', 'resource_id'], 'A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setVisible(false);


        $rowCount = 2;

        foreach ($this->project->resources as $resource) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource->resource_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource->name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource->types->root->name ?? '');

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource->versionFor($this->project->id)->rate);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, isset($resource->versionFor($this->project->id)->units->type) ? $resource->versionFor($this->project->id)->units->type : '');

            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $resource->versionFor($this->project->id)->waste);

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $resource->reference);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, isset(BusinessPartner::find($resource->business_partner_id)->name) ? BusinessPartner::find($resource->business_partner_id)->name : '');
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, isset($this->project_name) ? $this->project_name : '');
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $resource->id);
            $rowCount++;

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

}
