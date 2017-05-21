<?php

$data = json_decode($issue->data);

$activities = collect(json_decode($issue->data, true) ?: [])->map(function ($log) {
    if (isset($log['resource'])) {
        $attributes = $log['resource'];
    } else {
        $attributes = $log;
    }

    $log['resource'] = new \App\BreakDownResourceShadow($attributes);
    $log['resource']->id = $attributes['id'];
    return $log;
})->groupBy(function ($log) {
    if (isset($log['resource'])) {
        $resource = $log['resource'];
    } else {
        $resource = $log;
    }

    return $resource->wbs->path . ' / ' . $resource->activity;
})->sortByKeys();

$headerStyles = [
    'font' => ['bold' => true,],
    'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'FBE4D0']]
];

if ($activities->count()) {
    foreach ($activities as $activity => $resources) {
        $sheet->setCellValue("A{$counter}", $activity);
        $sheet->mergeCells("A{$counter}:G{$counter}");
        $sheet->getCell("A$counter")->getStyle()->applyFromArray($headerStyles);
        ++$counter;

        foreach ($resources as $log) {
            if (isset($log['resource'])) {
                $resource = $log['resource'];
            } else {
                $resource = $log;
            }

            $sheet->fromArray([
                $resource->resource_code,
                $resource->resource_name,
                $resource->budget_unit,
                $log['to_date_qty']?? 0,
                $log['remaining_qty']?? 0,
                $resource->progress
            ], '', "A{$counter}");

            ++$counter;
        }

        ++$counter;
    }
}
