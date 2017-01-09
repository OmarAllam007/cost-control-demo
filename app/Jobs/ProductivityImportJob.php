<?php

namespace App\Jobs;

use App\CsiCategory;
use App\Productivity;

class ProductivityImportJob extends ImportJob
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
        $productivities = Productivity::query()->pluck('code');
        $productivities2 = Productivity::query()->pluck('csi_code');
        $status = ['success' => 0, 'failed' => collect(),'dublicated'=>[]];
        CsiCategory::flushEventListeners();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            if (!$productivities->contains($data[0]) && !$productivities2->contains($data[0])) {
                $unit = $this->getUnit($data[6]);
                $item = [
                    'code' => $data[0],
                    'csi_code' => $data[0],
                    'description' => $data[5],
                    'csi_category_id' => $this->getDivisionId($data),
                    'unit' => $unit,
                    'crew_structure' => $data[7],
                    'daily_output' => $data[8],
                    'reduction_factor' => $data[9],
                    'source' => $data[10],
                ];

                if ($unit) {
                    Productivity::create($item);
                    ++$status['success'];
                } else {
                    $item['orig_unit'] = $data[6];
                    $status['failed']->push($item);
                }
            }else{

                if(!isset($status['dublicated'][$data[0]])){
                    $status['dublicated'][$data[0]] = $data[0];
                }
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

        $levels = array_filter(array_slice($data, 1, 4));
        $division_id = 0;
        $path = [];
        foreach ($levels as $level) {
            $path[] = mb_strtolower($level);
            $key = implode('/', $path);

            if ($this->division->has($key)) {
                $division_id = $this->division->get($key);
            } else {
                $division = CsiCategory::create([
                    'name' => $level,
                    'parent_id' => $division_id,
                ]);
                $division_id = $division->id;
                $this->division->put($key, $division_id);
            }
        }

        return $division_id;
    }

    protected function getAfterFactor($factor, $daily)
    {
        $after = floatval((1 - $factor) * $daily);
        return $after;
    }
}
