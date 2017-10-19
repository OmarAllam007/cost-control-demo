<?php

namespace App\Jobs\Export;

use App\Jobs\Job;


class ExportProductivityJob extends Job
{

    public $project;
    public function __construct($project)
    {
        $this->project = $project;

    }


    public function handle()
    {
        set_time_limit(600);
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);

        $sheet = $excel->getActiveSheet();

        $sheet->fromArray([
            'Code', 'Category Name', 'Description', 'Crew Structure', 'Daily Output', 'After Reduction',
            'Reduction Factor', 'Unit', 'Source'
        ], '', 'A1');

        $sheet->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);

        $rowCount = 2;
        foreach ($this->project->productivities as $productivity) {
            $sheet->fromArray([
                $productivity->csi_code, $productivity->category->path, $productivity->description,
                $productivity->crew_structure, $productivity->daily_output,
                $productivity->after_reduction, $productivity->reduction_factor, $productivity->units->type,
                $productivity->source
            ], '', "A{$rowCount}");

            $rowCount++;
        }

        foreach (['A', 'E', 'F', 'G', 'H', 'I'] as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        foreach (['B', 'C', 'D'] as $c) {
            $sheet->getColumnDimension($c)->setWidth(50);
        }

        $sheet->getStyle("E2:G{$rowCount}")
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $filename = storage_path("app/productivity_{$this->project->id}_" . uniqid() . '.xlsx');
        $writer->save($filename);

        return $filename;
    }
}
