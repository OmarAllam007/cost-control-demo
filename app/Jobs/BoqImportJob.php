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

class BoqImportJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $units;
    protected $file;
    protected $types;
    protected $project_id;


    public function __construct($project,$file)
    {
        $this->file = $file;
        $this->project_id = $project;
    }

    public function handle()
    {

        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);

        $rows = $sheet->getRowIterator(2);
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            foreach ($cells as $index => $cell) {
                $data = $this->getDataFromCells($cells);
                $item = Boq::where('item_code',$data[0])->first();
                dump($data);
                if(!$item) {
                    Boq::create([
                        'item_code' => $data[0],
                        'cost_account' => $data[1],
                        'wbs_id' => $this->getWbsId($data[2]),
                        'item' => $data[3],
                        'description' => $data[4],
                        'type' => $data[5],
                        'unit_id' => $this->getUnit($data[6]),
                        'quantity' => $data[7],
                        'dry_ur' => $data[8],
                        'price_ur' => $data[9],
                        'arabic_description' => $data[10],
                        'division_id' => $this->getDivisionId($data),
                        'kcc_qty' => $data[14],
                        'subcon' => $data[15],
                        'materials' => $data[16],
                        'manpower' => $data[17],
                        'project_id'=>$this->getProjectId($data[2]),
                    ]);
                }
                else{
                    continue;
                }
            }
        }

        unlink($this->file);
    }

    protected function getDivisionId($data)
    {
        $this->loadTypes();

        $levels = array_filter(array_slice($data, 11, 3));
        $type_id = 0;
        $path = [];
        foreach ($levels as $level) {
            $path[] = mb_strtolower($level);
            $key = implode('/', $path);

            if ($this->types->has($key)) {
                $type_id = $this->types->get($key);
            } else {
                $division = BoqDivision::create([
                    'name' => $level,
                    'parent_id' => $type_id
                ]);
                $type_id = $division->id;
                $this->types->put($key, $type_id);
            }
        }

        return $type_id;
    }

    protected function getProjectId($wbs_level){
        $level = WbsLevel::where('code','LIKE',$wbs_level.'%')->first();
        return $level->project_id;
    }

    protected function getDataFromCells($cells)
    {
        $data = [];
        /** @var \PHPExcel_Cell $cell */
        foreach ($cells as $cell) {
            if ($cell->getFormattedValue()) {
                $data[] = $cell->getFormattedValue();
            } else {
                $data[] = $cell->getValue();
            }
        }
        return $data;
    }

    protected function getWbsId($wbs_code)
    {
        $level = WbsLevel::where('code','LIKE',$wbs_code.'%')->first();

        return $level->id;

    }

    protected function getUnit($unit)
    {
        if (!$this->units) {
            $this->units = collect();
            Unit::all()->each(function ($unit) {
                $this->units->put(mb_strtolower($unit->type), $unit->id);
            });
        }
        $unit = trim($unit);

        if (!$unit) {
            return 0;
        }

        $key = mb_strtolower($unit);
        if ($this->units->has($key)) {
            return $this->units->get($key);
        }

        $unitObject = Unit::create(['type' => $unit]);
        $this->units->put(mb_strtolower($unit), $unitObject->id);
        return $unitObject->id;
    }

    private function loadTypes()
    {
        if ($this->types) {
            return $this->types;
        }

        $this->types = collect();
        BoqDivision::all()->each(function($type) {
            $this->types->put(mb_strtolower($type->canonical), $type->id);
        });

        return $this->types;
    }

}
