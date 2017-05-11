<?php

$excel = PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-std-activity.xlsx'));
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
    static $styleArray = ['font' => ['bold' => true]];

    if ($parent) {
        ++$outlineLevel;
        if ($outlineLevel >= 7) {
            $outlineLevel = 7;
        }
    }

    foreach ($tree->where('parent', $parent) as $name => $level) {
        $sheet->fromArray([
            str_repeat("    ", $outlineLevel) . $name, $level['budget_cost'] ?: 0, $level['previous_cost'] ?: 0, $level['previous_allowable'] ?: 0, $level['previous_var'] ?: 0,
            $level['to_date_cost'] ?: 0, $level['to_date_allowable'] ?: 0, $level['to_date_var'] ?: 0, $level['remaining_cost'] ?: 0,
            $level['completion_cost'] ?: 0, $level['completion_var'] ?: 0,
        ], 0, "A{$counter}");

        $sheet->getCell("A$counter")->getStyle()->applyFromArray($styleArray);
        if ($parent) {
            $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);
        }



        ++$counter;
        if ($tree->where('parent', $name)->count()) {
            $counter = renderLevel($tree, $sheet, $name, $counter, $outlineLevel);
        }

        if (!empty($level['activities'])) {
            foreach ($level['activities'] as $activity) {
                $sheet->fromArray($arr = [
                    str_repeat("    ", $outlineLevel + 1) . $activity['name'], $activity['budget_cost'] ?: 0, $activity['previous_cost'] ?: 0, $activity['previous_allowable'] ?: 0,
                    $activity['previous_var'] ?: 0, $activity['to_date_cost'] ?: 0, $activity['to_date_allowable'] ?: 0, $activity['to_date_var'] ?: 0,
                    $activity['remaining_cost'] ?: 0, $activity['completion_cost'] ?: 0, $activity['completion_var'] ?: 0,
                ], '', "A{$counter}");

                $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel + 1)->setVisible(false)->setCollapsed(true);
                ++$counter;
            }
        }
    }

    return $counter;
}

$counter = renderLevel($tree, $sheet, '', $counter);

$sheet->fromArray([
    "Totals", $currentTotals['budget_cost'] ?: 0, $previousTotals['previous_cost'] ?: 0, $previousTotals['previous_allowable'] ?: 0,
    $previousTotals['previous_var'] ?: 0, $currentTotals['to_date_cost'] ?: 0, $currentTotals['to_date_allowable'] ?: 0, $currentTotals['to_date_var'] ?: 0,
    $currentTotals['remaining'] ?: 0, $currentTotals['at_completion_cost'] ?: 0, $currentTotals->cost_var ?: 0,
], '', "A{$counter}");

$totalsStyles = $sheet->getStyle("A{$counter}:Y{$counter}");
$totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
$totalsStyles->getFont()->setBold(true);

$sheet->getStyle("B{$start}:Y{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
$sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("H{$start}:H{$counter}")->setConditionalStyles([$varCondition]);
$sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);

$sheet->setShowGridlines(false);

$saveTo = storage_path('app/') . uniqid() . '.xlsx';
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->setIncludeCharts(true)->save($saveTo);
echo $saveTo;
