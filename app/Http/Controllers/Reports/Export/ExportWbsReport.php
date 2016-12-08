<?php
namespace App\Http\Controllers\Reports\Export;

use App\Http\Requests\Request;
use App\Jobs\CacheWBSTree;
use App\Project;

class ExportWbsReport
{

    function exportWbsReport(Project $project)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        /** @var $objPHPExcel ->getActiveSheet(); $sheet */

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->fromArray(['WBS_LEVEL-1', 'WBS_LEVEL-2', 'WBS_LEVEL-3', 'WBS_LEVEL-4'], 'A1');
        $rowCount = 2;
        $col = 0;
        $varnumber = 5;
        $colstart = 4;
        $data = dispatch(new CacheWBSTree($project));
        $item = $this->getDepth($data);
        dd($item);
        foreach ($data as $level) {
            $sheet->setCellValueByColumnAndRow($col, $rowCount, $level['name']);
            if (count($level['children'])) {
                $col++;
                foreach ($level['children'] as $fChild) {
                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $fChild['name']);
                    if (count($fChild['children'])) {
                        $rowCount++;
                        $col++;
                        foreach ($fChild['children'] as $sChild) {
                            $sheet->setCellValueByColumnAndRow($col, $rowCount, $sChild['name']);
                            $col++;
                            if (count($sChild['children'])) {
                                $rowCount++;
                                foreach ($sChild['children'] as $thChild) {
                                    $sheet->setCellValueByColumnAndRow($colstart, 1, 'WBS_LEVEL-' . $varnumber);
                                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $thChild['name']);
                                    $colstart++;
                                    $varnumber++;
                                    $col++;
                                }
                                $rowCount--;
                            }
                        }
                        $col = 1;
                    }
                    $rowCount++;
                }
                $rowCount++;
            }


        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - WBSReport.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }
    function getDepth($a) {
        $max=0;
        foreach ($a as $val) {
            if (is_array($val)) {
                $tmp_depth=$this->getDepth($val);
                if ($max<($tmp_depth)) {
                    $max=$tmp_depth;
                }
            }
        }
        return $max+1;
    }


}