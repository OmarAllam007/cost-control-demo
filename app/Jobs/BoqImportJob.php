<?php

namespace App\Jobs;

use App\Boq;
use App\BoqDivision;
use App\Jobs\Job;
use App\Project;
use App\Unit;
use App\WbsLevel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BoqImportJob extends ImportJob
{
    protected $units;
    protected $file;
    protected $division;
    protected $project_id;


    public function __construct($project, $file)
    {
        $this->file = $file;
        $this->project_id = $project->id;
    }

    public function handle()
    {
        ini_set('max_execution_time', 300);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $status = ['success' => 0, 'failed' => collect(), 'project_id' => $this->project_id];

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $wbs_id = $this->getWbsId($data[0]);
            $division_id = $this->getDivisionId($data);
            $unit_id = $this->getUnit($data[8]);

            $boq = [
                'project_id' => $this->project_id, 'wbs_id' => $wbs_id,
                'item_code' => $data[1] ?: '', 'cost_account' => $data[2] ?: '', 'type' => $data[3] ?: '',
                'division_id' => $division_id, 'unit_id' => $unit_id,
                'description' => $data[7] ?: '',
                'quantity' => $data[9] ?: 0, 'price_ur' => $data[10] ?: 0, 'dry_ur' => $data[11] ?: 0, 'kcc_qty' => $data[12] ?: '',
                'materials' => $data[13] ?: '', 'subcon' => $data[14] ?: '', 'manpower' => $data[15] ?: '',
            ];

            if ($wbs_id && $unit_id) {
                Boq::create($boq);
                ++$status['success'];
            } else {
                if (!$wbs_id) {
                    $boq['orig_wbs_id'] = $data[0];
                }

                if (!$unit_id) {
                    $boq['orig_unit_id'] = $data[8];
                }

                $status['failed']->push($boq);
            }

        }

        unlink($this->file);
        return $status;
    }


    protected function getWbsId($wbs_code)
    {
        $level = WbsLevel::where('code', $wbs_code)->first();

        if (!$level) {
            return 0;
        }
        return $level->id;

    }

    protected function getDivisionId($data)
    {
        $this->loadDivision();

        $levels = array_filter(array_slice($data, 4, 3));
        $division_id = 0;
        $path = [];
        foreach ($levels as $level) {
            $path[] = mb_strtolower($level);
            $key = implode('/', $path);

            if ($this->division->has($key)) {
                $division_id = $this->division->get($key);
            } else {
                $division = BoqDivision::create([
                    'name' => $level,
                    'parent_id' => $division_id
                ]);
                $division_id = $division->id;
                $this->division->put($key, $division_id);
            }
        }

        return $division_id;
    }

    private function loadDivision()
    {
        if ($this->division) {
            return $this->division;
        }

        $this->division = collect();
        BoqDivision::all()->each(function ($division) {
            $this->division->put(mb_strtolower($division->canonical), $division->id);
        });

        return $this->division;
    }

    public static function checkImportData($data)
    {
        $errors = [];

        foreach ($data['units'] as $unit => $unit_id) {
            if (empty($unit_id)) {
                $errors['units.' . $unit] = $unit;
            }
        }

        foreach ($data['wbs'] as $wbs => $wbs_id) {
            if (empty($wbs_id)) {
                $errors['wbs.' . $wbs] = $wbs;
            }
        }

        return $errors;
    }
}
