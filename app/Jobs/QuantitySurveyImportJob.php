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
        $status = ['status'=>0,'failed'=>collect(),'dublicated'=>[]];
        $rows = $excel->getSheet(0)->getRowIterator(2);
        foreach ($rows as $row) {
            $cells = $this->getDataFromCells($row->getCellIterator());

            $filtered = array_filter($cells);
            if (!$filtered) {
                continue;
            }

            $wbs_level_id = $this->getWBSLevel($cells[0]);
            $level = WbsLevel::find($wbs_level_id);
            $level_survey = Survey::where('wbs_level_id', $level->id)->first();
            if ($level_survey) {
                $cost_accounts[] = $level_survey->cost_account;
            }
            $parent = $level;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parent_survey = Survey::where('wbs_level_id', $parent->id)->first();
                if ($parent_survey) {
                    $cost_accounts[] = $parent_survey->cost_account;
                }
            }

            if (in_array($cells[1], $cost_accounts)) {
                flash('Found dublicated Cost Account', 'danger');

            } else {
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
                    ++$status;
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