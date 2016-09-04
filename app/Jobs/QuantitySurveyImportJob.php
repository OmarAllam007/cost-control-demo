<?php

namespace App\Jobs;

use App\Project;
use App\Survey;
use App\WbsLevel;

class QuantitySurveyImportJob extends ImportJob
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var
     */
    private $file;

    protected $wbsLevels;

    function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;

        $this->wbsLevels = collect();
    }

    function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator();
        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row->getCellIterator());

            $wbs_level_id = $this->getWBSLevel($data[0]);
            $unit_id = $this->getUnit($data[5]);

            Survey::create([
                'project_id' => $this->project->id,
                'cost_account' => $data[1],
                'wbs_level_id' => $wbs_level_id,
                'description' => $data[2],
                'unit_id' => $unit_id,
                'budget_qty' => $data[3],
                'eng_qty' => $data[4]
            ]);
        }
    }

    protected function getWBSLevel($code)
    {
        if (!$this->wbsLevels->count()) {
            $this->project->wbs_levels->each(function($level) {
                $this->wbsLevels->put(mb_strtolower($level->code), $level->id);
            });
        }

        return $this->wbsLevels->get(mb_strtolower($code));
    }
}