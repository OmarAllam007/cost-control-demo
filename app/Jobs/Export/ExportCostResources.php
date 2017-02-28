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

        foreach ($this->resources as $resource) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource['code']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource['name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource['type']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource['rate']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $resource['measure_unit']);

            $rowCount++;
        }


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="CostResources'.$this->project->name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}
