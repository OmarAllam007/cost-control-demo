<?php
namespace App\Http\Controllers\Reports\Export;

use App\Http\Requests\Request;
use App\Jobs\CacheWBSTree;
use App\Project;
use phpDocumentor\Reflection\Types\Null_;

class ExportWbsReport
{

    function exportWbsReport(Project $project)
    {
        set_time_limit(600);
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        /** @var $objPHPExcel ->getActiveSheet(); $sheet */

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->fromArray(['WBS_LEVEL-1', 'WBS_LEVEL-2', 'WBS_LEVEL-3', 'WBS_LEVEL-4', 'WBS_LEVEL-5', 'WBS_LEVEL-6', 'WBS_LEVEL-7'], 'A1');
        $rowCount = 2;
        $col = 0;
        $data = dispatch(new CacheWBSTree($project));
        foreach ($data as $level) {
            $rowCount++;
            $sheet->setCellValueByColumnAndRow($col, $rowCount, $level['name']);
            if (count($level['children'])) {
                $col++;
                foreach ($level['children'] as $fChild) {
                    $rowCount++;
                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $fChild['name']);
                    if (count($fChild['children'])) {
                        $col++;
                        foreach ($fChild['children'] as $sChild) {
                            $rowCount++;
                            $sheet->setCellValueByColumnAndRow($col, $rowCount, $sChild['name']);
                            if (count($sChild['children'])) {
                                $col++;
                                foreach ($sChild['children'] as $thChild) {
                                    $rowCount++;
                                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $thChild['name']);
                                    if (count($thChild['children'])) {
                                        $col++;
                                        foreach ($thChild['children'] as $fourthChild) {
                                            $rowCount++;
                                            $sheet->setCellValueByColumnAndRow($col, $rowCount, $fourthChild['name']);

                                            if (count($fourthChild['children'])) {
                                                $col++;
                                                foreach ($fourthChild['children'] as $fifthChild) {
                                                    $rowCount++;
                                                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $fifthChild['name']);

                                                    if (count($fifthChild['children'])) {
                                                        $col++;
                                                        foreach ($fifthChild['children'] as $sixthChild) {
                                                            $rowCount++;
                                                            $sheet->setCellValueByColumnAndRow($col, $rowCount, $sixthChild['name']);
                                                            if (count($sixthChild['children'])) {
                                                                $col++;
                                                                foreach ($sixthChild['children'] as $seventhChild) {
                                                                    $rowCount++;
                                                                    $sheet->setCellValueByColumnAndRow($col, $rowCount, $seventhChild['name']);
                                                                }
                                                                $col--;
                                                            }
                                                        }
                                                        $col--;
                                                    }
                                                }
                                                $col--;
                                            }
                                        }
                                        $col--;
                                    }
                                }
                                $col--;
                            }
                        }
                        $col = 1;
                    }
                }
//                $rowCount++;
            }

            $col = 0;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - WBSReport.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    function getDepth($a)
    {
        $max = 0;
        foreach ($a as $val) {
            if (is_array($val)) {
                $tmp_depth = $this->getDepth($val);
                if ($max < ($tmp_depth)) {
                    $max = $tmp_depth;
                }
            }
        }
        return $max + 1;
    }


}
