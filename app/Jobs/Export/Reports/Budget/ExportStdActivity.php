<?php

namespace App\Jobs\Export\Reports\Budget;

use App\ActivityDivision;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportStdActivity extends Job
{

    protected $project;
    protected $objPHPExcel;
    protected $active_sheet;
    protected $rowCount;
    protected $col;

    public function __construct($project)
    {
        $this->project = $project;
        $this->objPHPExcel = new \PHPExcel();
        $this->objPHPExcel->setActiveSheetIndex(0);
        $this->active_sheet = $this->objPHPExcel->getActiveSheet()->fromArray(['Division', 'Sub Division 1', 'Sub Division 2', 'Activity'], 'A1');
        $this->rowCount = 2;
        $this->col = 0;
    }


    public function handle()
    {

        $div_ids = $this->project->getDivisions();
        $activity_ids = $this->project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];
        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();

        foreach ($parents as $division) {

            $this->objPHPExcel->getActiveSheet()->SetCellValue('A' . $this->rowCount, $division->label);
            $this->rowCount++;
            if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count()) {
                $this->col++;
                foreach ($division->children()->whereIn('id', $all)->get() as $fChild) {
                    $this->objPHPExcel->getActiveSheet()->SetCellValue('B' . $this->rowCount, $division->label);
                    $this->rowCount++;

                    if ($fChild->children()->whereIn('id', $all)->get() && $fChild->children()->whereIn('id', $all)->count()) {
                        foreach ($fChild->children()->whereIn('id', $all)->get() as $sChild) {
                            $this->objPHPExcel->getActiveSheet()->SetCellValue('C' . $this->rowCount, $division->label);
                            $this->rowCount++;

                            if ($sChild->activities()->whereIn('id', $activity_ids)->get() && $sChild->activities()->whereIn('id', $activity_ids)->count()) {
                                $this->col++;
                                foreach ($sChild->activities()->whereIn('id', $activity_ids)->get() as $activity) {
                                    $this->objPHPExcel->getActiveSheet()->SetCellValue('D' . $this->rowCount, $activity->name);
                                    $this->rowCount++;
                                }
                            }
                        }
                    }


                    if ($fChild->activities()->whereIn('id', $activity_ids)->get() && $fChild->activities()->whereIn('id', $activity_ids)->count()) {
                        $this->col++;
                        foreach ($fChild->activities()->whereIn('id', $activity_ids)->get() as $activity) {
                            $this->objPHPExcel->getActiveSheet()->SetCellValue('D' . $this->rowCount, $activity->name);
                            $this->rowCount++;
                        }
                    }
                }



            }


            if ($division->activities()->whereIn('id', $activity_ids)->get() && $division->activities()->whereIn('id', $activity_ids)->count()) {
                foreach ($division->activities()->whereIn('id', $activity_ids)->get() as $activity) {
                    $this->objPHPExcel->getActiveSheet()->SetCellValue('D' . $this->rowCount, $activity->name);
                    $this->rowCount++;
                }
            }
            $this->col=0;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->project->name . ' - Std-Activity.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($this->objPHPExcel);
        $objWriter->save('php://output');
    }


}
