<?php

namespace App\Import\Boq;


use App\Boq;
use App\BoqDivision;
use App\Jobs\CacheBoqTree;
use App\Project;
use App\Unit;
use App\UnitAlias;
use Illuminate\Support\Collection;

class BoqImport
{
    /** @var Project */
    private $project;
    private $file;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $units;

    /** @var Collection */
    protected $divisions;

    /** @var Collection */
    protected $failed;

    function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;

        $this->loadModels();
    }

    function import()
    {
        $excel = \PHPExcel_IOFactory::load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(1);
        $status = ['success' => 0, 'failed' => ''];

        $first = $rows->current();
        $cells = $first->getCellIterator();
        $this->headers = array();
        foreach ($cells as $col => $cell) {
            $this->headers[] = $cell->getValue();
        }

        $rows = $sheet->getRowIterator(2);

        Boq::flushEventListeners();

        $rules = config('validation.boq');
        $this->failed = collect();

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
                'wbs_id' => $this->wbs_levels->get(strtolower($data["A"])),
                'item_code' => $data["B"],
                'cost_account' => $data["A"] . '.' . $data["C"],
                'type' => $data["D"] ?: '',
//                'division_id' => $this->getDivisionId($data) ?: '',
                'description' => $data["E"] ?: '',
                'unit_id' => $this->units->get(strtolower($data["F"])) ?: 0,
                'quantity' => $data["G"] ?: 0,
                'price_ur' => $data["H"] ?: 0,
                'dry_ur' => $data["I"] ?: 0,
                'kcc_qty' => $data["J"] ?: 0,
                'materials' => $data["K"] ?: 0,
                'subcon' => $data["L"] ?: 0,
                'manpower' => $data["M"] ?: 0,
                'project_id' => $this->project->id,
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
                $data["N"] = implode(PHP_EOL, $errors);
                $this->failed->push($data);
            }
        }

        $status['failed'] = $this->generateFailed();

        \Cache::forget('boq-' . $this->project->id);
        \Cache::add('boq-' . $this->project->id, dispatch(new CacheBoqTree($this->project)), 7 * 24 * 60);

        //TODO: Save imported file

        return $status;
    }

    protected function loadModels()
    {
        $this->wbs_levels = $this->project->wbs_levels()->get(['id', 'code'])->map(function ($level) {
            return ['id' => $level->id, 'code' => strtolower($level->code)];
        })->pluck('id', 'code');

        $aliases = UnitAlias::all(['unit_id', 'name'])->map(function ($unit) {
            return ['id' => $unit->id, 'code' => strtolower($unit->name)];
        })->pluck('id', 'code');

        $this->units = Unit::all(['id', 'type'])->map(function ($unit) {
            return ['id' => $unit->id, 'code' => strtolower($unit->type)];
        })->pluck('id', 'code')->merge($aliases);

        /*$this->divisions = BoqDivision::all()->map(function ($division) {
            return ['code' => strtolower($division->canonical), 'id' => $division->id];
        })->pluck('id', 'code');*/
    }

    protected function generateFailed()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();

        $this->headers[] = "Errors";
        $sheet->fromArray($this->headers, '', "A1");

        $row_num = 1;
        foreach ($this->failed as $row) {
            ++$row_num;
            $sheet->fromArray(array_values($row),null,"A{$row_num}", true);
        }

        $filename = storage_path("app/public/boq_import_failed_{$this->project->id}_" . date('YmdHis') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel,'Excel2007')->save($filename);
        return "/storage/" . basename($filename);
    }

//    protected function getDivisionId($data)
//    {
//        $levels = array_filter(array_slice($data, 4, 3));
//        $division_id = 0;
//        $path = [];
//        foreach ($levels as $level) {
//            $path[] = strtolower($level);
//            $key = implode('/', $path);
//
//            if ($this->divisions->has($key)) {
//                $division_id = $this->divisions->get($key);
//            }
//        }
//
//        return $division_id;
//    }


}