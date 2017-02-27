<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\ResourceCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportResourcesMapping extends Job
{

    private $project;
    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        set_time_limit(600);
        $items = ResourceCode::where('project_id', $this->project->id)->get();

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'App Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Store Code');
        $rowCount = 2;

        foreach ($items as $item) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->resource->resource_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->code);
            $rowCount++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
