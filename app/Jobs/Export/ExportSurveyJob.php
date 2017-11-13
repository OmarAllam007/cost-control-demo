<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Survey;
use App\Unit;
use App\WbsLevel;


class ExportSurveyJob extends Job
{
    public $project;
    private $units;
    private $wbs_levels;
    private $variables;
    public function __construct($project)
    {
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id',$project->id)->get()->keyBy('id')->map(function ($level){
            return $level;
        });

        $this->units = Unit::all()->pluck('id', 'type');
    }


    public function handle()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getSheet(0);
        $counter = 2;

        $sheet->fromArray([
            'WBS Code', 'Item Code', 'Cost Account', 'Description', 'Budget Quantity', 'Engineer Quantity', 'Unit',
            'v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8'
        ], null, "A1", true);

        foreach ($this->project->quantities as $qs) {
            $wbs = $this->wbs_levels->get($qs->wbs_level_id);
            $data = [
                $wbs->code, $qs->item_code, $qs->cost_account, $qs->description,
                $wbs->budget_qty, $wbs->eng_qty, $this->units->get($qs->unit_id)
            ];

            foreach ($qs->variables as $idx => $variable){
                $data[] = $variable->value;
            }

            $sheet->fromArray($data, null, "A{$counter}", true);
            $counter++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.slug($this->project->name).'- Survey.xlsx"');
        header('Cache-Control: max-age=0');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }
}
