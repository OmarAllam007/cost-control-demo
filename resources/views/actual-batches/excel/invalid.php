<?php

$data = json_decode($issue->data);

if ($data) {
    foreach ($data as $row) {
        $sheet->fromArray($row, '', "A{$counter}");
        ++$counter;
    }
}

foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}
