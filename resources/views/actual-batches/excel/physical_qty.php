<?php

$data = json_decode($issue->data, true);
$splitterBorderColor = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
foreach ($data as $resource) {
    $index = 0;
    $rowsCount = count($resource['rows']);
    $mergeMax = $counter + $rowsCount - 1;

    foreach ($resource['rows'] as $row) {
        if ($index == 0) {
            $shadow = App\BreakDownResourceShadow::find($resource['resource']['id']);

            if (!$shadow) {
                break;
            }

            $sheet->getCell("A{$counter}")
                ->setValue($shadow->wbs->path . ' / ' . $shadow->activity)
                ->getStyle()->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("B{$counter}", $shadow->resource_name);
            $sheet->setCellValue("C{$counter}", $shadow->measure_unit);

            if ($rowsCount > 1) {
                $sheet->mergeCells("A{$counter}:A{$mergeMax}");
                $sheet->mergeCells("B{$counter}:B{$mergeMax}");
                $sheet->mergeCells("C{$counter}:C{$mergeMax}");
            }
        }

        $offset = $counter + $index;
        $sheet->setCellValue("D{$offset}", $row[2]);
        $sheet->setCellValue("E{$offset}", $row[3]);
        $sheet->setCellValue("F{$offset}", $row[4]);
        $sheet->setCellValue("G{$offset}", $row[5]);

        if ($index == 0) {
            $sheet->setCellValue("H{$counter}", $resource['newResource'][4]);
            $sheet->setCellValue("I{$counter}", $resource['newResource'][5]);
            $sheet->setCellValue("J{$counter}", $resource['newResource'][6]);

            if ($rowsCount > 1) {
                $sheet->mergeCells("H{$counter}:H{$mergeMax}");
                $sheet->mergeCells("I{$counter}:I{$mergeMax}");
                $sheet->mergeCells("J{$counter}:J{$mergeMax}");
            }
        }
        ++$index;
    }

    $lastRow = $counter + $index - 1;
    $sheet->getStyle("A{$lastRow}:J{$lastRow}")
        ->getBorders()->getBottom()->setBorderStyle('medium')
        ->setColor($splitterBorderColor);

    $counter = $lastRow + 1;
}