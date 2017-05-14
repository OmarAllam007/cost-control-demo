<?php

$excel = PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-boq.xlsx'));
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
    ->setCoordinates('P2')->setWorksheet($sheet);

$start = 10;
$counter = $start;

function renderWBS(PHPExcel_Worksheet $sheet, \Illuminate\Support\Collection $tree, $key, $level, $counter, $depth = 0)
{
    $sheet->fromArray([
        str_repeat('    ', $depth) . $level['name'],
        '', '', '', '', '', '', '',
        $level['dry_cost'] ?: '0.00',
        $level['boq_cost'] ?: '0.00',
        $level['budget_cost'] ?: '0.00',
        $level['to_date_cost'] ?: '0.00',
        $level['to_date_allowable'] ?: '0.00',
        $level['to_date_var'] ?: '0.00',
        $level['remaining_cost'] ?: '0.00',
        $level['at_completion_cost'] ?: '0.00',
        $level['at_completion_var'] ?: '0.00',
    ], '', "A{$counter}");

    if ($depth > 0) {
        $sheet->getRowDimension($counter)->setOutlineLevel($depth)->setCollapsed(true)->setVisible(false);
    }
    ++$counter;

    $children = $tree->where('parent', $key)->sortBy('name') ;
    if ($children->count()) {
        foreach($children as $subkey => $child) {
            $counter = renderWBS($sheet, $tree, $subkey, $child, $counter, $depth + 1);
        }
    }

    $depth += 1;

    if (!empty($level['boqs'])) {

        foreach($level['boqs'] as $boq) {
            $sheet->fromArray([
                str_repeat('    ', $depth) . $boq['cost_account'],
                $boq['description'],
                $boq['dry_price'] ?: '0.00',
                $boq['boq_price'] ?: '0.00',
                $boq['budget_unit_rate'] ?: '0.00',
                $boq['boq_qty'] ?: '0.00',
                $boq['budget_qty'] ?: '0.00',
                $boq['physical_qty'] ?: '0.00',
                $boq['dry_cost'] ?: '0.00',
                $boq['boq_cost'] ?: '0.00',
                $boq['budget_cost'] ?: '0.00',
                $boq['to_date_cost'] ?: '0.00',
                $boq['to_date_allowable'] ?: '0.00',
                $boq['to_date_var'] ?: '0.00',
                $boq['remaining_cost'] ?: '0.00',
                $boq['at_completion_cost'] ?: '0.00',
                $boq['at_completion_var'] ?: '0.00',
            ], '', "A{$counter}");
            $sheet->getRowDimension($counter)->setOutlineLevel($depth)->setCollapsed(true)->setVisible(false);
            ++$counter;
        }
    }

    return $counter;
}

foreach($tree->where('parent', '')->sortBy('name') as $key => $level) {
    $counter = renderWBS($sheet, $tree, $key, $level, $counter);
}

$sheet->getStyle("B{$start}:Q{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
$sheet->getStyle("N{$start}:N{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("N{$start}:Q{$counter}")->setConditionalStyles([$varCondition]);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
