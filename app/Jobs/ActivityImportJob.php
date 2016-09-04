<?php

namespace App\Jobs;

use App\ActivityDivision;
use App\BreakdownTemplate;
use App\StdActivity;
use Illuminate\Support\Collection;

class ActivityImportJob extends Job
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

        $rows = $excel->getSheet(0)->getRowIterator();

        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row->getCellIterator());

            $division_id = $this->getDivisionId($data);
            $activity_id = $this->getActivity($data, $division_id);

            BreakdownTemplate::create(['name' => $data[4], 'code' => $data[5], 'std_activity_id' => $activity_id]);
        }
    }

    protected function getDataFromCells(\PHPExcel_Worksheet_CellIterator $cells)
    {
        $data = [];
        /** @var \PHPExcel_Cell $cell */
        foreach ($cells as $cell) {
            $data[] = $cell->getValue();
        }
        return $data;
    }

    protected function getDivisionId($data)
    {
        if (!$this->divisions) {
            $this->divisions = collect();
            ActivityDivision::all()->each(function($division) {
                $this->divisions->put(mb_strtolower($division->canonical), $division->id);
            });
        }

        $tokens = array_filter(array_slice($data, 3));
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
                'name' => $token
            ]);
            $division_id = $division->id;
            $this->divisions->put($key, $division_id);
        }

        return $division_id;
    }

    private function getActivity($data, $division_id)
    {
        if (!$this->activities) {
            $this->activities = collect();
            StdActivity::all()->each(function($activity){
                $this->activities->put(mb_strtolower($activity->name), $activity->id);
            });
        }

        $name = $data[3];
        $key = mb_strtolower($name);

        if ($this->activities->has($key)) {
            return $this->activities->get($key);
        }

        $activity = StdActivity::create(compact('name', 'division_id'));
        $this->activities->put($key, $activity->id);
        return $activity->id;
    }
}
