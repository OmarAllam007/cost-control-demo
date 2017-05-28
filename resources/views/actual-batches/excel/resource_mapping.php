<?php

$counter = 2;
$data = json_decode($issue->data, true);

$topStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'E66826']]
];

if ($issue->type == 'resource_mapping_privileged') {
    $header = $sheet->fromArray([
        'Store Code', 'Mapped Code'
    ], '', "A1");

    $headerStyles = [
        'font' => ['bold' => true,],
        'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'FBE4D0']]
    ];

    $sheet->getStyle("A1:B1")->applyFromArray($topStyle);


    if ($data['mapping']) {
        $sheet->mergeCells('A2:B2')->getStyle('A2:B2')->applyFromArray($headerStyles);
        $sheet->setCellValue('A2', 'Mapped');

        foreach ($data['mapping'] as $store => $budget) {
            ++$counter;
            $sheet->fromArray([$store, $budget],'',"A$counter");
        }

        $counter += 2;
    }

    if ($data['ignored']) {
        $sheet->mergeCells("A$counter:B$counter")->getStyle("A$counter:B$counter")->applyFromArray($headerStyles);
        $sheet->setCellValue("A$counter", 'Ignored');

        foreach ($data['ignored'] as $store) {
            ++$counter;
            $sheet->fromArray([$store],'',"A$counter");
        }
    }

} else {
    $header = $sheet->fromArray([
        'Group / C.C.', 'DATE', 'Item Desc', 'UOM', 'Quantity', 'Unit Cost', 'Amount', 'ITEM CODES', 'Doc #'
    ], '', "A1");

    $sheet->getStyle("A1:I1")->applyFromArray($topStyle);

    foreach ($data as $row) {
        $sheet->fromArray($row, '', "A{$counter}");
        ++$counter;
    }
}

$sheet->calculateColumnWidths();