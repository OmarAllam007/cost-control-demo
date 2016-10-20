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

class BoqImportJob extends ImportJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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

        $boqs = Boq::query()->pluck('cost_account')->toArray();
        $notImported = [];
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);
            $key = in_array($data[0], $boqs);
            if (!$key) {
                Boq::create([
                    'wbs_id' => $this->getWbsId($data[0]) ?: 0,
                    'item_code' => $data[1] ?: '',
                    'cost_account' => $data[2] ?: '',
                    'type' => $data[3] ?: '',
                    'division_id' => $this->getDivisionId($data) ?: '',
                    'description' => $data[7] ?: '',
                    'unit_id' => $this->getUnit($data[8]) ?: 0,
                    'quantity' => $data[9] ?: 0,
                    'price_ur' => $data[10] ?: 0,
                    'dry_ur' => $data[11] ?: 0,
                    'kcc_qty' => $data[12] ?: '',
                    'materials' => $data[13] ?: '',
                    'subcon' => $data[14] ?: '',
                    'manpower' => $data[15] ?: '',
                    'project_id' => $this->project_id,
                ]);
            } else {
                $notImported[ $data[2] ] = $data[7];
            }
        }
        unlink($this->file);
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
                    'parent_id' => $division_id,
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

}
