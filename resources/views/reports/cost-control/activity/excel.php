<?php

$excel = PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-activity.xlsx'));
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
    ->setCoordinates('J2')->setWorksheet($sheet);

$start = 11;
$counter = $start;




function renderLevel($tree, PHPExcel_Worksheet $sheet, $parent, $counter, $outlineLevel = 0)
{
    $styleArray = ['font' => ['bold' => true]];

    if ($parent) {
        ++$outlineLevel;
        if ($outlineLevel >= 7) {
            $outlineLevel = 7;
        }
    }

    foreach ($tree->where('parent', $parent) as $name => $level) {
        $sheet->fromArray([
            str_repeat('   ', $outlineLevel) . $level['name'],
            $level['budget_cost'] ?: '0.00',
            $level['prev_cost'] ?: '0.00',
            $level['prev_allowable'] ?: '0.00',
            $level['prev_cost_var'] ?: '0.00',
            $level['to_date_cost'] ?: '0.00',
            $level['to_date_allowable'] ?: '0.00',
            $level['to_date_var'] ?: '0.00',
            $level['remaining_cost'] ?: '0.00',
            $level['completion_cost'] ?: '0.00',
            $level['completion_var'] ?: '0.00',
        ], '', "A{$counter}");

        $sheet->getCell("A$counter")->getStyle()->applyFromArray($styleArray);
        if ($parent) {
            $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);
        }

        ++$counter;
        if ($tree->where('parent', $name)->count()) {
            $counter = renderLevel($tree, $sheet, $name, $counter, $outlineLevel);
        }

        if (!empty($level['activities'])) {
            foreach ($level['activities'] as $name => $activity) {
                $sheet->fromArray($arr = [
                    str_repeat('    ', $outlineLevel + 1) . $name,
                    $activity['budget_cost'] ?: '0.00',
                    $activity['prev_cost'] ?: '0.00',
                    $activity['prev_allowable'] ?: '0.00',
                    $activity['prev_cost_var'] ?: '0.00',
                    $activity['to_date_cost'] ?: '0.00',
                    $activity['to_date_allowable'] ?: '0.00',
                    $activity['to_date_var'] ?: '0.00',
                    $activity['remaining_cost'] ?: '0.00',
                    $activity['completion_cost'] ?: '0.00',
                    $activity['completion_var'] ?: '0.00',
                ], '', "A{$counter}");

                $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel + 1)->setVisible(false)->setCollapsed(true);
                ++$counter;
            }
        }
    }

    return $counter;
}

$counter = renderLevel($tree, $sheet, '', $counter);

$sheet->getStyle("B{$start}:K{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');

$sheet->getStyle("B{$start}:K{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
$sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("G{$start}:G{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);

$sheet->setShowGridlines(true);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
