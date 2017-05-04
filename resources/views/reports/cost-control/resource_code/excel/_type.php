<?php
$totals = $typeData->reduce(function ($totals, $disciplineData) {
    return $disciplineData->reduce(function ($totals, $topMaterialData) {
        return $topMaterialData->reduce(function ($totals, $row) {
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
    }, $totals);
}, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0, 'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

$sheet->fromArray([
    $name,
    '', '',
    $totals['budget_cost'],
    '', '',
    $totals['prev_cost'],
    '', '',
    $totals['curr_cost'],
    '', '', '', '',
    $totals['to_date_cost'],
    $totals['to_date_allowable'],
    $totals['to_date_cost_var'],
    '', '',
    $totals['remaining_cost'],
    '', '', '',
    $totals['at_completion_cost'],
    $totals['cost_var'],
    ''
], '', "A$counter");

$sheet->getCell("A$counter")->getStyle()->applyFromArray($bold);
$typeStyle = $sheet->getStyle("A$counter:Z$counter");
$typeStyle->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE));
$typeStyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('FF' . dechex(131).dechex(163).dechex(206)));


foreach ($typeData as $discipline => $disciplineData) {
    ++$counter;
    include __DIR__  . DIRECTORY_SEPARATOR . '_discipline.php';
}
