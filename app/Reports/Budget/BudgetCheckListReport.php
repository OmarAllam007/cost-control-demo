<?php

namespace App\Reports\Budget;

use App\Project;

class BudgetCheckListReport
{
    /** @var Project */
    protected $project;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $excel = \PHPExcel_IOFactory::load(storage_path('templates/budget_checklist.xlsx'));
        $sheet = $excel->getActiveSheet();
        $sheet->setCellValue('B5', "Project: {$this->project->name}")
            ->setCellValue('B6', date('d-M-Y'));

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

        $filename = storage_path('app/check_list_' . uniqid() . '.xlsx');
        $writer->save($filename);

        return \Response::download($filename, slug($this->project->name) . '-budget_checklist.xlsx', [
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8'
        ])->deleteFileAfterSend(true);
    }

    function excel()
    {
        $this->run();
    }
}
