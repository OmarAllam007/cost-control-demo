<?php

$data = json_decode($issue->data);

if ($data) {
    foreach ($data as $row) {
        $cells = (array) $row;
        unset($cells['hash']);
        $sheet->fromArray($cells, '', "A{$counter}");
        ++$counter;
    }
}

foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}
