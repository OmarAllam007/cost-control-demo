<?php

$totals = $disciplineData->reduce(function($totals, $topMaterial) {
    $totals = $topMaterial->reduce(function($totals, $row){
        $totals['budget_cost'] += $row->budget_cost;
        $totals['prev_cost'] += $row->prev_cost;
        $totals['curr_cost'] += $row->curr_cost;
        $totals['to_date_cost'] += $row->to_date_cost;
        $totals['remaining_cost'] += $row->remaining_cost;
        $totals['at_completion_cost'] += $row->at_completion_cost;
        $totals['at_completion_var'] += $row->at_completion_var;
        $totals['cost_var'] += $row->cost_var;
        $totals['to_date_allowable'] += $row->to_date_allowable;
        $totals['to_date_cost_var'] = $totals['to_date_allowable'] - $totals['to_date_cost'];
        return $totals;
    }, $totals);

    return $totals;
}, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0,'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

$sheet->fromArray([
    '    ' . $discipline ?: 'General',
    '', '',
    $totals['budget_cost'] ?: 0,
    '', '',
    $totals['prev_cost'] ?: 0,
    '', '',
    $totals['curr_cost'] ?: 0,
    '', '', '', '',
    $totals['to_date_cost'] ?: 0,
    $totals['to_date_allowable'] ?: 0,
    $totals['to_date_cost_var'] ?: 0,
    '', '',
    $totals['remaining_cost'] ?: 0,
    '', '', '',
    $totals['at_completion_cost'] ?: 0,
    $totals['cost_var'] ?: 0,
    ''
], '', "A$counter");

$sheet->getCell("A$counter")->getStyle()->applyFromArray($bold);
$typeStyle = $sheet->getStyle("A$counter:Z$counter");
$typeStyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('FF' . dechex(212).dechex(228).dechex(238)));


$sheet->getRowDimension($counter)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);

foreach($disciplineData as $topMaterial => $topMaterialData){
    if ($topMaterial) {
        ++$counter;
        include __DIR__ . '/_top-material.php';
    }
}

$resources = $disciplineData->get('');
foreach($resources as $resource) {
    ++$counter;
    $outlineLevel = 2;
    include __DIR__ . '/_resource.php';
}