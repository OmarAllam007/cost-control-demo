<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 08/01/17
 * Time: 11:54 ص
 */

namespace App\Http\Controllers\Reports\Export;


use App\Productivity;

class ExportProductivityReport
{
    function exportProductivityReport($project)
    {
        set_time_limit(600);
        $data = [];
        $parents = [];
        $productivities = Productivity::where('project_id', $project->id)->get();
        foreach ($productivities as $productivity) {
            if (!isset($data[$productivity->category->id])) {
                $data[$productivity->category->id] = [
                    'name' => $productivity->category->name,
                    'productivities' => [],
                ];
            }
            $parent = $productivity->category;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parents[]=$parent;
            }
            $data[$productivity->category->id]['productivities'][$productivity->id]['name'] = $productivity->description;
            $data[$productivity->category->id]['productivities'][$productivity->id]['unit'] = $productivity->units->type;
            $data[$productivity->category->id]['productivities'][$productivity->id]['structure'] = $productivity->crew_structure;
            $data[$productivity->category->id]['productivities'][$productivity->id]['after_reduction'] = $productivity->after_reduction;

        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        /** @var $objPHPExcel ->getActiveSheet(); $sheet */

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->fromArray(['Division', 'Description', 'Unit', 'Crew Structure', 'Productivity'], 'A1');
        $rowCount = 2;
        $col = 0;
        foreach ($data as $item){
            $sheet->setCellValueByColumnAndRow($col,$rowCount,$item['name']);
            $rowCount++;
            $col++;
            if(count($item['productivities'])){
                foreach ($item['productivities'] as $productivity){
                    $sheet->setCellValueByColumnAndRow($col,$rowCount,$productivity['name']);
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col,$rowCount,$productivity['unit']);
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col,$rowCount,$productivity['structure']);
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)
                        ->getAlignment()
                        ->setWrapText(true);
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col,$rowCount,$productivity['after_reduction']);
                    $col=1;
                    $rowCount++;
                }
            }
            $col=0;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - WBSReport.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');

    }
}