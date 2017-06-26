<?php

$excel = PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-resource-dict.xlsx'));
$sheet = $excel->getActiveSheet();

$varCondition = new PHPExcel_Style_Conditional();
$varCondition->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
$varCondition->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
$varCondition->addCondition(0);
$varCondition->getStyle()->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

$projectCell = $sheet->getCell('A4');
$issueDateCell = $sheet->getCell('A5');
$periodCell = $sheet->getCell('A6');

$projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
$issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));
$periodCell->setValue($periodCell->getValue() . ' ' . $period->name);

$logo = imagecreatefrompng(public_path('images/kcc.png'));
$drawing = new PHPExcel_Worksheet_MemoryDrawing();
$drawing->setName('Logo')->setImageResource($logo)
    ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
    ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
    ->setCoordinates('X2')->setWorksheet($sheet);

$start = 12;
$counter = $start;

$bold = ['font' => ['bold' => true]];

$accent1 = [
    'fill' => [
        'type' => 'solid', 'startColor' => dechex(131).dechex(163).dechex(206)
    ],
    'font' => ['color' => 'ffffff']
];

foreach($tree as $name => $typeData){
    include __DIR__ . '/excel/_type.php';
    ++$counter;
}

$sheet->getStyle("B{$start}:Z{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');

$totalsStyles = $sheet->getStyle("A{$counter}:Z{$counter}");
$totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
$totalsStyles->getFont()->setBold(true);

$sheet->getStyle("N{$start}:N{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("Q{$start}:Q{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("W{$start}:W{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("Y{$start}:Y{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("Z{$start}:Z{$counter}")->setConditionalStyles([$varCondition]);

$sheet->setShowGridlines(false);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
