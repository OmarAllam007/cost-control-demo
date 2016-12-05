<?php

namespace App\Jobs;

use App\Http\Controllers\Exports\QuantitySurvey;
use App\Project;
use App\Survey;
use App\WbsLevel;

class QuantitySurveyImportJob extends ImportJob
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var
     */
    protected $file;

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

        $failed = collect();
        $success = 0;

        $rows = $excel->getSheet(0)->getRowIterator(2);
        $surveys = QuantitySurvey::query()->pluck('cost_account');

        foreach ($rows as $row) {
            $cells = $this->getDataFromCells($row->getCellIterator());

            $filtered = array_filter($cells);
            if (!$filtered) {
                continue;
            }

            if (!$surveys->contains($cells[1])) {
                $wbs_level_id = $this->getWBSLevel($cells[0]);
                $unit_id = $this->getUnit($cells[3]);

                $data = [
                    'project_id' => $this->project->id, 'cost_account' => $cells[1], 'wbs_level_id' => $wbs_level_id,
                    'description' => $cells[2], 'unit_id' => $unit_id, 'budget_qty' => $cells[4], 'eng_qty' => $cells[5],
                    'discipline' => isset($cells[6]) ? strtoupper($cells[6]) : ''
                ];

                if (!$wbs_level_id || !$unit_id) {
                    $data['wbs_code'] = $cells[0];
                    $data['unit'] = $cells[3];
                    $failed->push($data);
                } else {
                    Survey::create($data);
                    ++$success;
                }
            }

        }

        return ['project_id' => $this->project->id, 'failed' => $failed, 'success' => $success];
    }

    protected function getWBSLevel($code)
    {
        if (!$this->wbsLevels->count()) {
            $this->project->wbs_levels->each(function ($level) {
                $this->wbsLevels->put(mb_strtolower($level->code), $level->id);
            });
        }

        return $this->wbsLevels->get(mb_strtolower($code), 0);
    }
}