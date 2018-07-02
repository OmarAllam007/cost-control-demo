<?php
$activities = collect(json_decode($issue->data, true));
$wbs_ids = $activities->pluck('newRows.*.resource.wbs_id')->flatten()->unique();
$levels = \App\WbsLevel::find($wbs_ids->toArray())->keyBy('id')->map(function ($level) {
    return $level->path;
});

$activities = $activities->groupBy(function($row) use ($levels) {
    if (empty($row['newRows'][0]['resource'])) {
        return 'unassigned';
    }

    $resource = $row['newRows'][0]['resource'];
    return $levels[$resource['wbs_id']] . ' &mdash; ' . $resource['activity'];
})->forget('unassigned');
$counter = 2;


$headerStyles = [
    'font' => ['bold' => true,],
    'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'FBE4D0']]
];


foreach ($activities as $name => $data) {
    $sheet->mergeCells("A$counter:J$counter");
    $headerCell = $sheet->getCell("A$counter");
    $headerCell->setValue($name)->getStyle()->applyFromArray($headerStyles);

    ++$counter;

    foreach ($data as $resource) {

        $oldRow = $resource['oldRow'];
        $newRows = $resource['newRows'];
        $rowSpan = count($newRows);

        $mergeMax = $counter + $rowSpan - 1;

        if ($rowSpan > 1) {
            $sheet->mergeCells("A$counter:A$mergeMax");
            $sheet->mergeCells("B$counter:B$mergeMax");
            $sheet->mergeCells("C$counter:C$mergeMax");
            $sheet->mergeCells("D$counter:D$mergeMax");
            $sheet->mergeCells("E$counter:E$mergeMax");
        }

        $firstRow = array_shift($newRows);

        $sheet->fromArray([
            $oldRow[2], //A
            $oldRow[7], //B
            $oldRow[4], //C
            $oldRow[5], //D
            $oldRow[6], //E
            $firstRow['resource']['cost_account'], //F
            $firstRow['resource']['budget_unit'], //G
            $firstRow[4], //H
            $firstRow[5], //I
            $firstRow[6] //J
        ], '', "A$counter");

        ++$counter;

        foreach ($newRows as $row) {

            $sheet->fromArray([
                $row['resource']['cost_account'],
                $row['resource']['budget_unit'],
                $row[4],
                $row[5],
                $row[6],
            ], '', "F$counter");

            ++$counter;
        }

        $lastRow = $counter - 1;
        $sheet->getStyle("A{$lastRow}:J{$lastRow}")
            ->getBorders()->getBottom()->setBorderStyle('thin');
    }

    $counter += 4;
}

foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}