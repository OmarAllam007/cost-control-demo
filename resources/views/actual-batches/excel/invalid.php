<?php

$data = json_decode($issue->data);

if ($data) {
    foreach ($data as $row) {
        $sheet->fromArray($row, '', "A{$counter}");
        ++$counter;
    }
}