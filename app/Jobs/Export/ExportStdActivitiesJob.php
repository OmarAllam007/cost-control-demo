<?php

namespace App\Jobs\Export;

use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\StdActivity;


class ExportStdActivitiesJob extends ImportJob
{

    /**
     *
     */
    public function handle()
    {
        set_time_limit(600);
        $col = 0;
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        /** @var \PHPExcel $active_sheet */
        $active_sheet = $objPHPExcel->getActiveSheet();
        $active_sheet->fromArray(['Code', 'Name', 'Division', 'Discipline', 'Work Package Name'
            , 'Partial ID'], 'A1');
        $rowCount = 2;
        $varnumber = 1;
        $activities = StdActivity::all();
        $data = [];
        foreach ($activities as $activity) {
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->code);
            $col++;
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->name);
            $col++;
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->division->name);
            $col++;
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->discipline);
            $col++;
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->work_package_name);
            $col++;
            $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $activity->id_partial);
            $col++;

            if (count($activity->variables)) {
                foreach ($activity->variables as $variable) {

                    $active_sheet->setCellValueByColumnAndRow($col, 1, '$var' . $varnumber);
                    $active_sheet->setCellValueByColumnAndRow($col, $rowCount, $variable->label);
                    $col++;
                    $varnumber++;
                }
            }
            $varnumber = 1;
            $col = 0;
            $rowCount++;

        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'F8F8FF'),
                'size' => 15,
                'name' => 'Verdana',

            ));
        $active_sheet->getStyle('A1:' . $active_sheet->getHighestDataColumn() . $varnumber)
            ->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('1C86EE');


        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $active_sheet->getHighestDataColumn() . $varnumber)->applyFromArray($styleArray);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="All-StdActivities.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');


    }

}
