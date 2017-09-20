<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Project;
use App\ResourceCode;
use App\Resources;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportResourcesMapping extends Job
{
    /** @var Project */
    protected $project;

    public function __construct(Project $project)
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
        $sheet = $objPHPExcel->getSheet(0);
        set_time_limit(600);

        $resources = Resources::where('project_id', $this->project->id)->with('codes')->get();


        $sheet->SetCellValue('A1', 'App Code');
        $sheet->SetCellValue('B1', 'Store Code');
        $rowCount = 2;

        foreach ($resources as $resource) {
            foreach ($resource->codes as $code) {
                $sheet->SetCellValue('A' . $rowCount, $resource->resource_code);
                $sheet->SetCellValue('B' . $rowCount, $code->code);
                $sheet->SetCellValue('C' . $rowCount, $resource->name);
                $rowCount++;
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . slug($this->project->name) . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
