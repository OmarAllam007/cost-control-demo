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

    function Import()
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
                $failed->push($data);
                continue;
            }

            $this->surveys->push(new Survey($qs));
        }

        return $this->fix();
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

    protected function fix()
    {
        /** @var Collection $boqs */
        $boqs = $this->surveys->map(function($survey) {
            $boq_id = Boq::forQs($survey)->value('id');
            $survey->boq_id = $boq_id;

            return $survey;
        })->keyBy('boq_id')->reject(function($group)  {
            if ($group->count() > 1) {
                return false;
            }

            $survey = $group->first();

            $hasOtherSurveys = Survey::where('boq_id', $survey->boq_id)->exist();
            if ($hasOtherSurveys) {
                return false;
            }

            if ($survey->unit != $survey->boq->id) {
                return false;
            }

            $survey->save();

            // Create equivalent QS
            Survey::craete([
                'wbs_level_id' => $survey->boq->wbs_id, 'cost-account' => $survey->boq->cost_account,
                'description' => $survey->boq->description, 'boq_id' => $survey->boq_id, 'unit_id' => $survey->unit,
                'budget_qty' => $survey->budget_qty, 'eng_qty' => $survey->eng_qty
            ]);

            return true;
        });

        if ($boqs->count()) {
            $cacheKey = uniqid('qs-boq-map-', true);
            \Cache::put($cacheKey, ['boqs' => $boqs, 'success' => $this->counter, 'project' => $this->project]);
            return \Redirect::route('qty-survey.fix-boq', $cacheKey);
        }

        if ($this->failed->count()) {
            $failed = $this->generateFailedExport();
            return view('qty-survey.import-failed', ['success' => $this->counter, 'project' => $this->project, 'failed' => $failed]);
        }

        flash("{$this->counter} Qty survey items have been imported");
        return \Redirect::route('project.budget', $this->project);
    }

    protected function generateFailedExport()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();

        $headers = ['WBS Code', 'Item Code', 'Description', 'Budget Qty', 'Eng. Qty', 'Unit', 'Errors'];
        $sheet->fromArray($headers, null, 'A1', true);

        $counter = 2;
        foreach ($this->failed as $row) {
            $sheet->fromArray($row, null, "A{$counter}", true);
            ++$counter;
        }

        $sheet->getStyle("D2:E{$counter}")->getNumberFormat()->setFormatCode('#,##0.00');

        $filename = storage_path('app/qs-failed-' . date('YmdHis'));
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return '/storage/' . basename($filename);
    }
}