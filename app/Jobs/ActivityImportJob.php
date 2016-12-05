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
//to make columns dynamic
            $status = ['failed' => collect(), 'success' => 0, 'dublicated' => []];
            $activities = StdActivity::query()->pluck('code');
            $highest = \PHPExcel_Cell::columnIndexFromString($excel->getSheet(0)->getHighestColumn());

            if (!$activities->contains($data[$highest - 3])) {
                $division_id = $this->getDivisionId($data, $highest);
                $work_package_name = $data[$highest - 5];
                $name = $data[$highest - 4];
                $code = $data[$highest - 3];
                $id_partial = $data[$highest - 2];
                $discipline = strtoupper($data[$highest - 1]);
                $activity = StdActivity::create(['name' => $name, 'division_id' => $division_id, 'code' => $code, 'work_package_name' => $work_package_name, 'id_partial' => $id_partial, 'discipline' => $discipline]);
                $cellCount = count($data);
                if ($cellCount > 10) {
                    $display_order = 1;
                    for ($i = 10; $i < $cellCount; ++$i) {
                        $label = trim($data[$i]);
                        if ($label) {
                            $activity->variables()->create(compact('label', 'display_order'));
                            ++$display_order;
                        }
                    }
                }

                ++$status['success'];
            } else {
                $status['dublicated'][] = $data[$highest - 3];
            }

        }

        return $status;
    }

    protected function getDivisionId($data, $highest)
    {
        if (!$this->divisions) {
            $this->divisions = collect();
            ActivityDivision::all()->each(function ($division) {
                $this->divisions->put(mb_strtolower($division->canonical), $division->id);
            });

        }

        $tokens = array_filter(array_slice($data, 0, $highest - 5));
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
