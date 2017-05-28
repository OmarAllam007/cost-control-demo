<?php

$counter = 2;
$data = json_decode($issue->data, true);

$topStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'E66826']]
];

$header = $sheet->fromArray(['Activity', 'Cost Account', 'Resource', 'Remarks', 'Progress'], '', "A1");

$headerStyles = [
    'font' => ['bold' => true,], 'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'FBE4D0']]
];

$sheet->getStyle("A1:E1")->applyFromArray($topStyle);

if ($data['reopened']) {
    $sheet->mergeCells('A2:E2')->getStyle('A2:E2')->applyFromArray($headerStyles);
    $sheet->setCellValue('A2', 'Reopened');

    foreach ($data['reopened'] as $row) {
        $shadow = \App\BreakDownResourceShadow::find($row['id']);

        ++$counter;
        $sheet->fromArray([
            $shadow->wbs->path . '/' . $shadow->activity, $shadow->cost_account, $shadow->resource_name,
            $shadow->remarks, $row['progress'] ?? '',
        ], '', "A$counter");
    }

    $counter += 2;
}

if ($data['ignored']) {
    $sheet->mergeCells("A$counter:E$counter")->getStyle("A$counter:E$counter")->applyFromArray($headerStyles);
    $sheet->setCellValue("A$counter", 'Ignored');

    foreach ($data['ignored'] as $row) {
        $shadow = \App\BreakDownResourceShadow::find($row['id']);

        ++$counter;
        $sheet->fromArray([
            $shadow->wbs->path . '/' . $shadow->activity, $shadow->cost_account, $shadow->resource_name, $shadow->remarks,
        ], '', "A$counter");
    }
}

foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}
