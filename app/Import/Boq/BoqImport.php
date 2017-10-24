<?php

namespace App\Import\Boq;


use App\Boq;
use App\Project;
use FontLib\TrueType\Collection;

class BoqImport
{
    /** @var Project */
    private $project;
    private $file;

    function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;

        $this->wbs_levels = $this->project->wbs_levels()->get(['id', 'code'])->map(function($level){
            return ['id' => $level->id, 'code' => strtolower($level->code)];
        })->pluck('id', 'code');
    }

    function import()
    {
        $excel = \PHPExcel_IOFactory::load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $status = ['success' => 0, 'failed' => ''];

        Boq::flushEventListeners();
        /** @var Collection $boqs */
        $boqs = Boq::with('wbs')->where('project_id', $this->project->id)->get()->keyBy(function ($item) {
            return strtolower($item->wbs->code . $item->cost_account);
        });

        $rules = config('validation.boq');
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = array();
            foreach ($cells as $col => $cell) {
                $data[$col] = $cell->getValue();
            }

            if (!array_filter($data)) {
                continue;
            }

            $boq = [
                'wbs_id' => $this->getWbsId($data["A"]),
                'item_code' => $data["B"],
                'cost_account' => $data["C"],
                'type' => $data["D"] ?: '',
                'division_id' => $this->getDivisionId($data) ?: '',
                'description' => $data["H"] ?: '',
                'unit_id' => $this->getUnit($data["I"]) ?: 0,
                'quantity' => $data["J"] ?: 0,
                'price_ur' => $data["K"] ?: 0,
                'dry_ur' => $data["L"] ?: 0,
                'kcc_qty' => $data["M"] ?: 0,
                'materials' => $data["N"] ?: 0,
                'subcon' => $data["O"] ?: 0,
                'manpower' => $data["P"] ?: 0,
                'project_id' => $this->project_id,
            ];

            $validator = \Validator::make($boq, $rules);
            $errors = [];
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
            }



            if (!$errors) {
                Boq::create($boq);
                ++$status['success'];
            } else  {
                $row["Q"] = implode(PHP_EOL, $errors);
                $status['failed'] = $row;
            }
        }

        \Cache::forget('boq-' . $this->project_id);
        \Cache::add('boq-' . $this->project_id, dispatch(new CacheBoqTree($this->project)), 7 * 24 * 60);

        unlink($this->file);
        return $status;
    }
}