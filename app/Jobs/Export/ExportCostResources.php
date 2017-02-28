<?php

namespace App\Jobs\Export;

use App\CostResource;
use App\Jobs\Job;
use App\Unit;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportCostResources extends Job
{

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $project;
    protected $units;
    protected $resources;

    public function __construct($project)
    {
        $this->project = $project;
        $this->units = Unit::pluck('type', 'id');
        $this->resources = CostResource::where('project_id', $project->id)
            ->where('period_id', $project->open_period()->id)
            ->get()->map(function (CostResource $resource) {
                return $resource->jsonFormat();
            });
    }


    public function handle()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'Resource Name', 'Resource Type', 'Rate', 'Unit Of Measure'], 'A1');
        $rowCount = 2;
        $column = 0;

        foreach ($this->resources as $resource) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource['code']);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource['name']);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource['type']);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource['rate']);
            $column++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $resource['measure_unit']);
            $column++;
            $rowCount++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Cost Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
