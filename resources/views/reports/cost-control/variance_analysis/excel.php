<?php

$excel = PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-var-analysis.xlsx'));
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
    ->setCoordinates('L2')->setWorksheet($sheet);

$start = 11;
$counter = $start;

$bold = ['font' => ['bold' => true]];

$accent1 = [
    'fill' => [
        'type' => 'solid', 'startColor' => dechex(131).dechex(163).dechex(206)
    ],
    'font' => ['color' => 'ffffff']
];

foreach($tree as $type => $typeData){

    ++$counter;

    $sheet->fromArray([
        $type, '', '', '', '', '',
        $typeData['price_cost_var'], '', '', '',
        $typeData['qty_cost_var']?: '0.00', //K
        $typeData['cost_unit_price_var']?: '0.00', //L
        $typeData['cost_qty_var']?: '0.00'
    ], '', "A{$counter}");
    $sheet->getCell("A{$counter}")->getStyle()->applyFromArray($bold);

    foreach ($typeData['disciplines'] as $discipline => $disciplineData) {
        ++$counter;
        $sheet->fromArray([
            '    ' . ($discipline?: 'General'), '', '', '', '', '',
            $disciplineData['price_cost_var'], '', '', '',
            $disciplineData['qty_cost_var']?: '0.00', //K
            $disciplineData['cost_unit_price_var']?: '0.00', //L
            $disciplineData['cost_qty_var']?: '0.00'
        ], '', "A{$counter}");
        $sheet->getRowDimension($counter)->setOutlineLevel(1)->setCollapsed(true)->setVisible(false);
        $sheet->getCell("A{$counter}")->getStyle()->applyFromArray($bold);

        foreach ($disciplineData['resources'] as $resource) {
            ++$counter;

            $sheet->fromArray([
                '        ' . $resource->resource_name, //A
                $resource->budget_unit_price?: '0.00', //B
                $resource->prev_unit_price?: '0.00', //C
                $resource->curr_unit_price?: '0.00', //D
                $resource->to_date_unit_price?: '0.00', //E
                $resource->price_var ?: '0.00', //F
                $resource->price_cost_var ?: '0.00', //G
                $resource->to_date_qty?: '0.00', //H
                $resource->to_date_allowable_qty?: '0.00', //I
                $resource->qty_var?: '0.00', //J
                $resource->qty_cost_var ?: '0.00', //K
                $resource->cost_unit_price_var?: '0.00', //L
                $resource->cost_qty_var?: '0.00', //M
            ], null, "A{$counter}", true);
            $sheet->getRowDimension($counter)->setOutlineLevel(2)->setCollapsed(true)->setVisible(false);
        }
    }

}

$sheet->getStyle("B{$start}:M{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');

/*$totalsStyles = $sheet->getStyle("A{$counter}:Z{$counter}");
$totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
$totalsStyles->getFont()->setBold(true);*/

$sheet->getStyle("F{$start}:F{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("G{$start}:G{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("J{$start}:J{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("L{$start}:L{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("M{$start}:M{$counter}")->setConditionalStyles([$varCondition]);


$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
