<?php
namespace App\Http\Controllers\Reports\Export;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\Jobs\Job;
use App\StdActivity;

class ExportStandardActivityReport
{
    public function exportStandardActivityReport($project)
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->fromArray(['Division-1', 'Division-2', 'Division-3', 'Division-4','activity'], NUll, 'A1');
        $sheet = $objPHPExcel->getActiveSheet();
        $rowCount = 2;
        $col=0;
        $division_ids = collect();
        $data=[];
        $activities_ids = BreakDownResourceShadow::where('project_id', $project->id)->pluck('activity_id')->unique()->toArray();


        StdActivity::whereIn('id', $activities_ids)->get()->map(function ($activity) use ($division_ids) {
            if (!$division_ids->contains($activity->division->id)) {
                $division_ids->push($activity->division->id);
            }
        });






        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - BOQ.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
}