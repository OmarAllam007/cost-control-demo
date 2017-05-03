<?php
$template = storage_path('app/templates/cost-summary.xlsx');
$reader = new PHPExcel_Reader_Excel2007();
$reader->setIncludeCharts(true);
$excel = $reader->load($template);

$sheet = $excel->getActiveSheet();

$varCondition = new PHPExcel_Style_Conditional();
$varCondition->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
$varCondition->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
$varCondition->addCondition(0);
$varCondition->getStyle()->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

$projectCell = $sheet->getCell('A4');
$issueDateCell = $sheet->getCell('A5');

$projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
$issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));

$start = 11;
$counter = $start;
foreach($resourceTypes as $id => $value) {
    $typePreviousData = $previousData[$id] ?? [];
    $typeToDateData = $toDateData[$id] ?? [];

    $row = [
        $value,
        $typeToDateData['budget_cost'] ?: '0.00',
        $typePreviousData['previous_cost'] ?: '0.00',
        $typePreviousData['previous_allowable'] ?: '0.00',
        $typePreviousData['previous_var'] ?: '0.00',
        $typeToDateData['to_date_cost'] ?: '0.00',
        $typeToDateData['to_date_allowable'] ?: '0.00',
        $typeToDateData['to_date_var'] ?: '0.00',
        $typeToDateData['remaining_cost'] ?: '0.00',
        $typeToDateData['completion_cost'] ?: '0.00',
        $typeToDateData['completion_cost_var'] ?: '0.00',
    ];

    $sheet->fromArray($row, '',"A{$counter}");
    ++$counter;
}

$row = [
    "Totals",
    $toDateData->sum('budget_cost'),
    $previousData->sum('previous_cost'),
    $previousData->sum('previous_allowable'),
    $previousData->sum('previous_var'),
    $toDateData->sum('to_date_cost'),
    $toDateData->sum('to_date_allowable'),
    $toDateData->sum('to_date_var'),
    $toDateData->sum('remaining_cost'),
    $toDateData->sum('completion_cost'),
    $toDateData->sum('completion_cost_var'),
];
$sheet->fromArray($row, '', "A{$counter}");
$sheet->setCellValue("A{$counter}", "Total");

$sheet->getStyle("A{$start}:A{$counter}")->getFont()->setBold(true);
$totalsStyles = $sheet->getStyle("A{$counter}:K{$counter}");
$totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
$totalsStyles->getFont()->setBold(true);

$sheet->getStyle("B{$start}:K{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
$sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("H{$start}:H{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
