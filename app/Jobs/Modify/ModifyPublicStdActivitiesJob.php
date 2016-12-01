<?php

namespace App\Jobs\Modify;

use App\ActivityDivision;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\StdActivity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyPublicStdActivitiesJob extends ImportJob
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
        $displayOrder = 6;
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }
            $std_Activity = StdActivity::where('code', $data[0])->first();
            if ($std_Activity) {
                $item = [
                    'name' => $data[1],
                    'division_id' => $this->getDivisionId($data[2]),
                    'discipline' => $data[3],
                    'work_package_name' => $data[4],
                    'id_partial' => $data[5],
                ];
                $std_Activity->update($item);
                foreach ($std_Activity->variables->sortBy('display_order') as $variable) {
                    $variable->label = $data[$displayOrder];
                    $variable->update();
                    $displayOrder++;
                }

            }
            $displayOrder = 6;
        }

    }

    protected function getDivisionId($data)
    {
        if (!$this->divisions) {
            $this->divisions = collect();
            ActivityDivision::all()->each(function ($division) {
                $this->divisions->put(mb_strtolower($division->canonical), $division->id);
            });

        }

        $division_id = 0;
        $path = [];

        $path[] = mb_strtolower($data);
        $key = implode('/', $path);
        if ($this->divisions->has($key)) {
            $division_id = $this->divisions->get($key);
        } else {
            $division = ActivityDivision::create([
                'parent_id' => $division_id,
                'name' => $data,

            ]);
            $division_id = $division->id;
            $this->divisions->put($key, $division_id);
        }


        return $division_id;
    }
}
