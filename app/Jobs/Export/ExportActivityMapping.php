<?php

namespace App\Jobs\Export;

use App\ActivityMap;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportActivityMapping extends Job
{


    protected $project;
    public function __construct($project)
    {
        $this->project = $project;

    }


    public function handle()
    {

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        set_time_limit(600);
        $items = ActivityMap::where('project_id', $this->project->id)->get();
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'App Activity Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Store Activity Code');
        $rowCount = 2;

        foreach ($items as $item) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->activity_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->equiv_code);
            $rowCount++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - ActivityMapping.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}
