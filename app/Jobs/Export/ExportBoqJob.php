<?php

namespace App\Jobs\Export;

use App\Boq;
use App\BoqDivision;
use App\Jobs\Job;
use App\Unit;
use App\WbsLevel;

class ExportBoqJob extends Job
{

    public $project;
    private $wbs_levels;
    private $boqs;
    private $units;

    public function __construct($project)
    {
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id',$project->id)->get()->keyBy('id')->map(function ($level){
            return $level;
        });
        $this->boqs = BoqDivision::all()->keyBy('id')->map(function ($division){
           return $division->name;
        });
        $this->units = Unit::all()->keyBy('id')->map(function ($unit){
            return $unit->type;
        });


    }

    public function handle()
    {
        $items = Boq::where('project_id', $this->project->id)->get();

        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->getSheet(0);
        $sheet->fromArray([
            'Item Code', 'Cost Account', 'Description', 'Discipline', 'Unit', 'Estimated Quantity',
            'Unit Price', 'Unit Dry', 'KCC-Quantity', 'Materials', 'SubContractors', 'Man Power',
            'WBS-LEVEL', 'WBS_PATH'],
            null, 'A1', true);

        $counter = 2;
        foreach ($items as $item) {
            $data = [
                $item->item_code, $item->cost_account, $item->description, $item->type,
                $this->units->get($item->unit_id), $item->quantity, $item->price_ur, $item->dry_ur, $item->kcc_qty,
                $item->materials, $item->subcon, $item->subcon, $this->wbs_levels->get($item->wbs_id)->path,
                $item->wbs->code
            ];

            $sheet->fromArray($data, null, "A{$counter}", true);
            ++$counter;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$this->project->name.'- BOQ.xlsx"');
        header('Cache-Control: max-age=0');

        \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007')
            ->save('php://output');
    }
}
