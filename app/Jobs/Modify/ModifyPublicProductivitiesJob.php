<?php

namespace App\Jobs\Modify;

use App\CsiCategory;
use App\Jobs\CacheCsiCategoryTree;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\Productivity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyPublicProductivitiesJob extends ImportJob
{
    protected $units;
    protected $file;
    protected $division;
    protected $project_id;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $status = ['success' => 0, 'failed' => collect()];

        CsiCategory::flushEventListeners();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            $productivity = Productivity::where('csi_code', $data[0])->first();

            $unit = $this->getUnit($data[6]);
            $division = $this->getDivisionId($data[1]);
            $item = [
                'code' => $data[0],
                'csi_code' => $data[0],
                'description' => $data[4],
                'csi_category_id' => $division,
                'unit' => $unit,
                'crew_structure' => $data[5],
                'daily_output' => $data[2],
                'reduction_factor' => $data[3],
                'source' => $data[7],
            ];

            if ($unit) {
                $productivity->update($item);
//                ++$status['success'];
            } else {
//                $item['orig_unit'] = $data[6];
//                $status['failed']->push($item);
            }
        }

        dispatch(new CacheCsiCategoryTree());
        unlink($this->file);

        return $status;
    }

    private function loadDivision()
    {
        if ($this->division) {
            return $this->division;
        }

        $this->division = collect();
        CsiCategory::all()->each(function ($division) {
            $this->division->put(mb_strtolower($division->canonical), $division->id);
        });

        return $this->division;
    }

    protected function getDivisionId($data)
    {
        $this->loadDivision();
        $division_id = 0;
        $path = [];
        $path[] = mb_strtolower($data);
        $key = implode('/', $path);

        if ($this->division->has($key)) {
            $division_id = $this->division->get($key);
        }
        return $division_id;
    }

    protected function getAfterFactor($factor, $daily)
    {
        $after = floatval((1 - $factor) * $daily);
        return $after;
    }
}
