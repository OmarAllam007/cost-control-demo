<?php

namespace App\Import\QtySurvey;

use App\Boq;
use App\Project;
use App\Survey;
use App\Unit;
use App\UnitAlias;
use Illuminate\Support\Collection;

class QtySurveyImport
{
    /**
     * @var Project
     */
    protected $project;
    protected $file;
    protected $headers = [];

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $units;

    /** @var Collection */
    protected $surveys;

    /** @var Collection */
    protected $failed;

    /** @var int */
    protected $counter = 0;


    function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();
        $this->surveys = collect();

        $this->loadModels();
    }

    function import()
    {
        $excel = \PHPExcel_IOFactory::load($this->file);


        $headerCells = $excel->getSheet(0)->getRowIterator(1)->current()->getCellIterator();
        foreach ($headerCells as $cell) {
            $this->headers[] = $cell->getValue();
        }

        $rules = config('validation.qty_survey');

        $rows = $excel->getSheet(0)->getRowIterator(2);
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = [];
            foreach ($cells as $col => $cell) {
                $data[$col] = $cell->getValue();
            }

            if (!array_filter($data)) {
                continue;
            }

            $qs = [
                'wbs_level_id' => $this->wbs_levels->get(strtolower($data['A'])),
                'item_code' => $data['B'],
                'description' => $data['C'],
                'budget_qty' => $data['D'],
                'eng_qty' => $data['E'],
                'unit_id' => $this->units->get($data['F']),
                'project_id' => $this->project->id
            ];

            $validator = \Validator::make($qs, $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $data['G'] = implode(PHP_EOL, $errors);
                $this->failed->push($data);
                continue;
            }

            $this->surveys->push(new Survey($qs));
        }

        $checker = new QtySurveyChecker($this->project, $this->surveys, $this->failed);
        return $checker->check();
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

        $this->boqs = Boq::where('project_id', $this->project->id)->get(['id', 'item_code'])->map(function ($boq) {
            return ['id' => $boq->id, 'code' => strtolower($boq->item_code)];
        })->pluck('id', 'code');
    }
}