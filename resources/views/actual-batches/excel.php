<?php

$excel = \PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-issue-log.xlsx'));

$counters = [
    'activity_mapping' => 2,
    'resource_mapping' => 2,
    'physical_qty' => 2,
    'closed_resources' => 2,
    'account_distribution' => 2,
    'progress' => 2,
    'status' => 2,
    'invalid' => 2,
];

$sheets = [
    'info' => $excel->getSheet(0),
    'activity_mapping' => $excel->getSheet(1),
    'resource_mapping' => $excel->getSheet(2),
    'physical_qty' => $excel->getSheet(3),
    'closed_resources' => $excel->getSheet(4),
    'account_distribution' => $excel->getSheet(5),
    'progress' => $excel->getSheet(6),
    'status' => $excel->getSheet(7),
    'invalid' => $excel->getSheet(8),
];

//TODO: Add logo

$sheets['info']->getCell('C5')->setValue($actual_batch->project->name);
$sheets['info']->getCell('C6')->setValue($actual_batch->period->name);
$sheets['info']->getCell('C7')->setValue($actual_batch->id);
$sheets['info']->getCell('C8')->setValue($actual_batch->user->name);
$sheets['info']->getCell('C9')->setValue($actual_batch->created_at->format('Y-m-d'));
$sheets['info']->setShowGridlines(false);

foreach ($actual_batch->issues as $issue) {
    $sheet = $sheets[$issue->type];
    $counter = $counters[$issue->type];
    $file = __DIR__ . '/excel/' . $issue->type . '.php';

    if (file_exists($file)) {
        include $file;
        $counters[$issue->type] = $counter;
    }
}

$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$filename = storage_path('app/actual_batch_' . $actual_batch->id . '.xlsx');
$writer->save($filename);
readfile($filename);
unlink($filename);
