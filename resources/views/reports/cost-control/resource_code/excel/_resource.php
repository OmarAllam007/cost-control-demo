<?php

$sheet->fromArray([
    str_repeat('    ', $outlineLevel) . $resource->resource_name,
    $resource->budget_qty? $resource->budget_cost / $resource->budget_qty : 0,
    $resource->budget_qty,
    $resource->budget_cost,
    $resource->prev_qty? $resource->prev_cost / $resource->prev_qty : 0,
    $resource->prev_qty,
    $resource->prev_cost,
    $resource->curr_qty? $resource->curr_cost / $resource->curr_qty : 0,
    $resource->curr_qty,
    $resource->curr_cost,
    $resource->to_date_qty? $resource->to_date_cost / $resource->to_date_qty : 0,
    $resource->to_date_qty,
    $resource->to_date_allowable_qty,
    $resource->to_date_allowable_qty - $resource->to_date_qty,
    $resource->to_date_cost,
    $resource->to_date_allowable,
    $resource->to_date_allowable - $resource->to_date_cost,
    $resource->remaining_qty? $resource->remaining_cost / $resource->remaining_qty : 0,
    $resource->remaining_qty,
    $resource->remaining_cost,
    $resource->at_completion_qty? $resource->at_completion_cost / $resource->at_completion_qty : 0,
    $resource->at_completion_qty,
    $resource->qty_var,
    $resource->at_completion_cost,
    $resource->cost_var,
    $resource->pw_index * 100,
], '', "A$counter");

$sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);