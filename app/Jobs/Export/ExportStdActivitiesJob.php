<?php

namespace App\Jobs\Export;

use App\Jobs\ImportJob;
use App\Support\ActivityDivisionTree;
use function array_merge;
use function array_pad;
use function array_reverse;
use function array_splice;
use function count;
use PHPExcel_Worksheet;
use SplStack;
use function storage_path;
use function uniqid;


class ExportStdActivitiesJob extends ImportJob
{
    private $row = 1;
    /** @var SplStack */
    private $divisionStack;

    public function __construct()
    {
        $this->divisionStack = new SplStack();
    }

    /**
     * @throws \PHPExcel_Exception
     */
    public function handle()
    {
        $objPHPExcel = new \PHPExcel();

        $sheet = $objPHPExcel->getSheet();
        $sheet->fromArray([
            'Code', 'Name', 'Division', 'Sub Division 1', 'Sub Division 2', 'Sub Division 3', 'Sub Division 4',
            'Discipline', 'Work Package Name', 'Partial ID', '$v1', '$v2', '$v3', '$v4', '$v5', '$v6', '$v7', '$v8',
            '$v9', '$v10'
        ], null,'A1', true);

        $divisionCache = new ActivityDivisionTree();
        $divisions = $divisionCache->get();
        foreach ($divisions as $division) {
            $this->buildDivision($sheet, $division);
        }

        $this->applyStyle($sheet);

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $filename = storage_path('app/std_activities_' . uniqid() . '.xlsx');
        $objWriter->save($filename);
        return $filename;
    }

    /**
     * @param $sheet PHPExcel_Worksheet
     * @param $division
     * @param $depth
     * @throws \PHPExcel_Exception
     */
    private function buildDivision($sheet, $division, $depth = 0)
    {
        while ($this->divisionStack->count() > $depth) {
            $this->divisionStack->pop();
        }

        $this->divisionStack->push($division->label);

        foreach ($division->activities->sortBy('code') as $activity) {
            ++$this->row;
            $data = [$activity->code, $activity->name];
            $data = array_merge($data, $this->getDivisions());
            $data = array_merge($data, [$activity->discipline, $activity->work_package_name, $activity->id_partial]);
            $sheet->fromArray($data, null, "A{$this->row}", true);

            if ($activity->variables->count()) {
                $sheet->fromArray($activity->variables->pluck('label')->toArray(), null,"K{$this->row}", true);
            }
        }

        foreach ($division->subtree as $subdivision) {
            $this->buildDivision($sheet, $subdivision, $depth + 1);
        }
    }

    private function getDivisions()
    {
        $divisions = [];
        foreach ($this->divisionStack as $division) {
            $divisions[] = $division;
        }

        $divisions = array_reverse($divisions);

        if (count($divisions) > 5) {
            return array_splice($divisions, 0, 5);
        }

        return array_pad($divisions, 5, '');
    }

    /**
     * @param $sheet
     */
    private function applyStyle($sheet): void
    {
        $styleArray = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'F8F8FF']],
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => '1C86EE']]
        ];

        $col = $sheet->getHighestColumn();

        foreach (range('A', $col) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $sheet->getStyle("A1:{$col}{$this->row}")->getNumberFormat()->setFormatCode('@');

        $sheet->getStyle("A1:{$col}1")->applyFromArray($styleArray);
        $sheet->setAutoFilter("A1:J{$this->row}");
    }

}
