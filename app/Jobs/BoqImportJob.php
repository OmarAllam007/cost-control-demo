<?php

namespace App\Jobs;

use App\Boq;
use App\BoqDivision;
use App\Jobs\Job;
use App\Project;
use App\Unit;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BoqImportJob extends ImportJob
{
    protected $units;
    protected $file;
    protected $division;
    protected $project_id;
    /**
     * @var
     */
    private $project;


    public function __construct($project, $file)
    {
        $this->file = $file;
        $this->project_id = $project->id;
        $this->project = $project;
    }

    public function handle()
    {

        ini_set('max_execution_time', 500);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $status = ['success' => 0, 'failed' => collect(), 'dublicated' => []];
        Boq::flushEventListeners();
        $boqs = Boq::with('wbs')->where('project_id', $this->project_id)->get()->map(function ($item) {
            if (isset($item->wbs->code)) {
                return mb_strtolower($item->wbs->code . $item->cost_account);
            }
        });

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);
            /** @var Collection $boqs */
            if (!array_filter($data)) {
                continue;
            }

            $code = mb_strtolower($data[0] . $data[2]);
            if (!$boqs->search($code) && $this->getWbsId($data[0]) != 0) {
                Boq::create([
                    'wbs_id' => $this->getWbsId($data[0]),
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
                ++$status['success'];
            } else if ($boqs->search($code)) {
                $status['dublicated'][] = $data[0];
            }
        }

        \Cache::forget('boq-' . $this->project_id);
        \Cache::add('boq-' . $this->project_id, dispatch(new CacheBoqTree($this->project)), 7 * 24 * 60);

        unlink($this->file);
        return $status;
    }


    protected function getWbsId($wbs_code)
    {
        $level = WbsLevel::forProject($this->project_id)->where('code', $wbs_code)->first();

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
