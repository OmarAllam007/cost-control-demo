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

    public function __construct ($project)
    {
        $this->project = $project;
        $this->units = Unit::pluck('type', 'id');
        if ($project->open_period()) {
            $this->resources = CostResource::where('project_id', $project->id)
                ->where('period_id', $project->open_period()->id)
                ->get()->map(function (CostResource $resource) {
                    return $resource->jsonFormat();
                });
        }

    }


    public function handle ()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray(['Code', 'Resource Name', 'Resource Type', 'Rate', 'Unit Of Measure'], 'A1');
        $rowCount = 2;
        if ($this->resources && $this->resources->count()) {

            foreach ($this->resources as $resource) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource['code']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource['name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource['type']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource['rate']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $resource['measure_unit']);

                $rowCount++;
            }
        }
        $project_name = $this->project->name;
        $xlsName = $project_name . "CostResources.xlsx";
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $xlsName . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}
