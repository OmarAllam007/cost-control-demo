<?php

namespace App\Jobs;

use App\ActivityDivision;
use App\BreakdownTemplate;
use App\StdActivity;
use Illuminate\Support\Collection;

class ActivityImportJob extends ImportJob
{
    protected $file;
    /**
     * @var Collection
     */
    protected $divisions;
    /**
     * @var Collection
     */
    protected $activities;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle()
    {

        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);
        $count = 0;

        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row->getCellIterator());
            if (!array_filter($data)) {
                continue;
            }
            $division_id = $this->getDivisionId($data);
            $work_package_name = $data[3];
            $name = $data[4];
            $code = $data[5];
            $id_partial = $data[6];
            $discipline = $data[7];
            $key = mb_strtolower($code);

            $activity = StdActivity::create(['name' => $name, 'division_id' => $division_id, 'code' => $code, 'work_package_name' => $work_package_name, 'id_partial' => $id_partial, 'discipline' => $discipline]);

            $count = count($data);
            if ($count > 8) {
                $display_order = 1;
                for ($i = 7; $i < $count; ++$i) {
                    $label = trim($data[$i]);
                    if ($label) {
                        $activity->variables()->create(compact('label', 'display_order'));
                        ++$display_order;
                    }
                }
            }

            ++$count;
        }

        return $count;
    }

    protected function getDivisionId($data)
    {
        if (!$this->divisions) {
            $this->divisions = collect();
            ActivityDivision::all()->each(function ($division) {
                $this->divisions->put(mb_strtolower($division->canonical), $division->id);
            });

        }

        $tokens = array_filter(array_slice($data, 0, 3));
        $division_id = 0;
        $path = [];

        foreach ($tokens as $token) {
            $path[] = mb_strtolower($token);
            $key = implode('/', $path);
            if ($this->divisions->has($key)) {
                $division_id = $this->divisions->get($key);
                continue;
            }

            $division = ActivityDivision::create([
                'parent_id' => $division_id,
                'name' => $token,

            ]);
            $division_id = $division->id;
            $this->divisions->put($key, $division_id);
        }

        return $division_id;
    }
}
