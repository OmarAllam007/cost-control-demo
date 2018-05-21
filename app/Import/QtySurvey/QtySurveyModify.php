<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 20/05/2018
 * Time: 12:09 PM
 */

namespace App\Import\QtySurvey;

use App\Unit;
use function collect;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use PHPExcel_IOFactory;
use function strtolower;

class QtySurveyModify
{
    /** @var \App\Project */
    private $project;

    private $file;

    /** @var Collection */
    private $wbs_levels;

    /** @var Collection */
    private $qty_surveys;

    /** @var Collection */
    private $failed;

    public function __construct($project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();

        $this->loadWbs();
        $this->loadQs();
        $this->loadUnits();
    }

    function import()
    {
        $sheet = PHPExcel_IOFactory::load($this->file)->getSheet(0);



        $rows = $sheet->getRowIterator(2);
        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row);

            $this->handleRow($data);
        }
    }

    function loadWbs()
    {
        $this->wbs_levels = $this->project->wbs_levels->keyBy(function ($level) {
            return strtolower($level->code);
        })->map(function ($level) {
            return $level->id;
        });
    }

    function loadQs()
    {
        $this->qty_surveys = $this->project->quantities
            ->groupBy('wbs_level_id')->map(function ($group) {
                return $group->groupBy(function ($qs) {
                    return strtolower($qs->cost_account);
                });
            });
    }

    private function getDataFromCells($row)
    {
        $cells = $row->getCellsIterator();
        $data = [];
        foreach ($cells as $col => $cell) {
            $data[$col] = $cell->getValue();
        }
        return $data;
    }

    private function handleRow($data)
    {
        $wbs_code = strtolower($data['B']);
        $wbs_id = $this->wbs_levels->get($wbs_code);
        if (!$wbs_id) {
            $data['T'] = 'WBS not found';
            $this->failed->push($data);
            return false;
        }

        $cost_account = strtolower($data['E']);
        $qty_survey = $this->qty_surveys->get($wbs_id, collect())->get($cost_account);
        if (!$qty_survey) {
            $data['T'] = 'Cost account not found';
            $this->failed->push($data);
            return false;
        }
        
        $unit = strtolower($data['I']);
        $unit_id = $this->units->get($unit);
        if (!$unit_id) {
            $data['T'] = 'Invalid unit of measure';
            $this->failed->push($data);
            return false;
        }

        $qty_survey->description = $data['F'];
        $qty_survey->budget_qty = $data['G'];
        $qty_survey->eng_qty = $data['H'];

        $qty_survey->save();

        $this->handleVariables($data);
    }

    private function loadUnits()
    {
        $this->units = Unit::select(['id', 'type'])->get()->keyBy(function($unit) {
            return strtolower($unit->type);
        })->map(function($unit) {
            return $unit->id;
        });
    }

    private function handleVariables($data)
    {
    }
}