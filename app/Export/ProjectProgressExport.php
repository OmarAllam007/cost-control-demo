<?php

namespace App\Export;

use App\Project;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet;

class ProjectProgressExport
{
    /** @var Project */
    private $project;

    private $row = 2;

    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * @throws \PHPExcel_Exception
     * @return string
     */
    public function handle()
    {
        $excel = new PHPExcel();
        $sheet = $excel->getSheet();

        $this->header($sheet);

        $activities = $this->project->shadows()->with('wbs')
            ->selectRaw('wbs_id, code, activity, avg(progress) as progress')
            ->where('show_in_cost', 1)
            ->groupBy(['wbs_id', 'code', 'activity'])
            ->get();

        foreach ($activities as $activity) {
            $sheet->fromArray([
                $activity->wbs->code, $activity->code, $activity->activity, $activity->progress ?? 0
            ], null, "A{$this->row}", true);

            ++$this->row;
        }

        $this->setStyle($sheet);
        $filename = storage_path('app/' . uniqid('progress_') . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return $filename;
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @throws \PHPExcel_Exception
     */
    private function header($sheet)
    {
        $sheet->fromArray([
            'Wbs Code', 'Activity Code', 'Activity Name', 'Progress (Average)'
        ], null, "A1", true);

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => '3490DC']]
        ]);
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @throws \PHPExcel_Exception
     */
    private function setStyle($sheet)
    {
        $sheet->getStyle("A2:C{$this->row}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("D2:D{$this->row}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setAutoFilter("A1:D{$this->row}");
    }
}