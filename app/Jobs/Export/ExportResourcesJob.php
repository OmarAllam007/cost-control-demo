<?php

namespace App\Jobs\Export;

use App\BusinessPartner;
use App\Jobs\Job;
use App\Unit;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportResourcesJob extends Job
{

    public $project;
    public $project_name;
    public $objPHPExcel;
    private $partners;
    private $units;

    public function __construct($project)
    {
        $this->project = $project;
        $this->project_name = $this->project->name;

        $this->partners = BusinessPartner::all()->keyBy('id')->map(function ($partner) {
            return $partner->name;
        });

        $this->units = Unit::pluck('type', 'id');
    }

    public function handle()
    {
//        set_time_limit(600);
//
//        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
//        $cacheSettings = array('memoryCacheSize' => '500MB', 'cacheTime' => '1000');
//        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'Resource Name', 'Resource Type 1', 'Resource Type 2', 'Resource Type 3', 'Resource Type 4', 'Rate', 'Unit'
            , 'Waste', 'reference', 'Business Partner', 'Project Name', 'resource_id'], 'A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setVisible(false);


        $rowCount = 2;
        $column = 0;

        foreach ($this->project->resources as $resource) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->resource_code);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->name);
            $column++;
            if(isset($resource->types->path)){
                $types = explode('»', $resource->types->path) ;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, trim($types[0] ?? ''));
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, trim($types[1] ?? ''));
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, trim($types[2] ?? ''));
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, trim($types[3] ?? ''));
                $column++;
            }
           else{
               $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->types);
               $column+=3;
           }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->rate);
            $column++;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $this->units->get($resource->unit));
            $column++;


            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->waste);
            $column++;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->reference);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $this->partners->get($resource->business_partner_id));
            $column++;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, isset($this->project_name) ? $this->project_name : '');
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource->id);

            $column = 0;
            $rowCount++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

}
