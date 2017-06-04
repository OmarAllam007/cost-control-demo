<?php
$template = storage_path('templates/cost-summary.xlsx');
$reader = new PHPExcel_Reader_Excel2007();
//$reader->setIncludeCharts(true);
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

$logo = imagecreatefrompng(public_path('images/kcc.png'));
$drawing = new PHPExcel_Worksheet_MemoryDrawing();
$drawing->setName('Logo')->setImageResource($logo)
    ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
    ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
    ->setCoordinates('J2')->setWorksheet($sheet);

$start = 11;
$counter = $start;
foreach ($resourceTypes as $id => $value) {
    $typePreviousData = $previousData[$id] ?? [];
    $typeToDateData = $toDateData[$id] ?? [];

    $row = [
        $value,
        $typeToDateData['budget_cost'] ?: '0.00',
        $typePreviousData['previous_cost'] ?? '0.00',
        $typePreviousData['previous_allowable'] ?? '0.00',
        $typePreviousData['previous_var'] ?? '0.00',
        $typeToDateData['to_date_cost'] ?: '0.00',
        $typeToDateData['to_date_allowable'] ?: '0.00',
        $typeToDateData['to_date_var'] ?: '0.00',
        $typeToDateData['remaining_cost'] ?: '0.00',
        $typeToDateData['completion_cost'] ?: '0.00',
        $typeToDateData['completion_cost_var'] ?: '0.00',
    ];

    $sheet->fromArray($row, '', "A{$counter}");
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


//<editor-fold defaultstate="collapsed desc="Budget Cost VS Completion Cost Chart">
$end = $counter - 1;

$xAxisLabels = [
    new PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!A{$start}:A{$end}", null, $counter - $start)
];

$budgetVsCompletionValues = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!B$start:B$end"),
    new PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!J$start:J$end"),
];

$budgetVsCompletionLabels = [
    new PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!B" . ($start - 1), NULL, 1),
    new PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!J" . ($start - 1), NULL, 1),
];

$budgetVsCompletionTitle = new PHPExcel_Chart_Title('Budget Cost vs At Completion');
$budgetVsCompletionLegend = new PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
$budgetVsCompletionDataSeries = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_BARCHART,
    PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
    [0, 1],
    $budgetVsCompletionLabels,
    $xAxisLabels,
    $budgetVsCompletionValues
);
$budgetVsCompletionDataSeries->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);

$budgetVsCompletionPlot = new PHPExcel_Chart_PlotArea(null, [$budgetVsCompletionDataSeries]);
$budgetVsCompletionChart = new PHPExcel_Chart(
    'budget_vs_comp', $budgetVsCompletionTitle, $budgetVsCompletionLegend,
    $budgetVsCompletionPlot, true, '0', null, null
);

$budgetVsCompletionChart->setTopLeftCell("A" . ($counter + 5))->setBottomRightCell('F' . ($counter + 25));
$sheet->addChart($budgetVsCompletionChart);
//</editor-fold>

//<editor-fold defaultstate="collapsed desc="To Date Vs Allowable Chart">
$todateVsAllowableValues = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!F$start:F$end"),
    new PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!G$start:G$end"),
];

$todateVsAllowableLabels = [
    new PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!F" . ($start - 1), NULL, 1),
    new PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!G" . ($start - 1), NULL, 1),
];

$todateVsAllowableTitle = new PHPExcel_Chart_Title('To Date vs Allowable');
$todateVsAllowableLegend = new PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
$todateVsAllowableDataSeries = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_BARCHART,
    PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
    [0, 1],
    $todateVsAllowableLabels,
    $xAxisLabels,
    $todateVsAllowableValues
);
$todateVsAllowableDataSeries->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);

$todateVsAllowablePlot = new PHPExcel_Chart_PlotArea(null, [$todateVsAllowableDataSeries]);
$todateVsAllowableChart = new PHPExcel_Chart(
    'budget_vs_comp', $todateVsAllowableTitle, $todateVsAllowableLegend,
    $todateVsAllowablePlot, true, '0', null, null
);

$todateVsAllowableChart->setTopLeftCell("G" . ($counter + 5))->setBottomRightCell('L' . ($counter + 25));
$sheet->addChart($todateVsAllowableChart);
//</editor-fold>

$sheet->setShowGridlines(false);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
