<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WbsLevelExportJob extends Job
{


    public $project;
    public function __construct($project)
    {
        $this->project = $project;
    }


    public function handle()
    {

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A1:D1')
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFCC')
                    )
                )
            );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'WBS-LEVEL 1');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'WBS-LEVEL 2');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'WBS-LEVEL 3');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'WBS-LEVEL 4');
        $rowCount = 2;
        foreach ($this->project->wbs_tree as $level) {

            $objPHPExcel->getActiveSheet()
                ->getStyle('A'.$rowCount.':D'.$rowCount)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'CCE5FF')
                        )
                    )
                );
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $level->name);
            $rowCount++;
            if ($level->children && $level->children->count()) {
                foreach ($level->children as $children) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $children->name);
                    $objPHPExcel->getActiveSheet()
                        ->getStyle('B'.$rowCount.':D'.$rowCount)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'FFE5CC')
                                )
                            )
                        );
                    $rowCount++;
                    if ($children->children && $children->children->count()) {
                        foreach ($children->children as $child) {
                            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $child->name);
                            $rowCount++;
                            if ($child->children && $child->children->count()) {
                                foreach ($child->children as $child) {
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $child->name);
                                    $rowCount++;
                                }
                            }
                        }
                    }

                }
            }

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$this->project->name.' - WBS Levels.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
}
