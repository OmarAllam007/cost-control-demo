<?php

namespace App\Jobs;

use App\CsiCategory;
use App\Jobs\Job;
use App\Productivity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductivityImportJob extends ImportJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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
        $productivities = Productivity::query()->pluck('code')->toArray();

        foreach ($rows as $row) {

            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);
            if (!(in_array($data[0], $productivities))) {
                Productivity::create([
                    'code' => $data[0],
                    'description' => $data[5],
                    'csi_category_id' => $this->getDivisionId($data),
                    'unit' => $this->getUnit($data[6]),
                    'crew_structure' => $data[7],
//                   'crew_hours' => $data[6],
//                    'crew_equip' => $data[7],
                    'daily_output' => $data[8],
//                    'man_hours' => $data[9],
                    'reduction_factor' => $data[9],
//                    'equip_hours' => $data[10],
                    'after_reduction' => $this->getAfterFactor($data[9], $data[8]),
                    'source' => $data[10],
                ]);

            }

        }

        unlink($this->file);
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
                    'parent_id' => $division_id
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
