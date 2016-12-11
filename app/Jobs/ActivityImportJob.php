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
        $headerArray = [];
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $workPKGIndex = $disciplineIndex = 0;
        $highest = \PHPExcel_Cell::columnIndexFromString($excel->getActiveSheet()->getHighestColumn());

        $header = $excel->getActiveSheet()->getRowIterator(1)->current();
        $cellIterator = $header->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        foreach ($cellIterator as $cell) {
            $headerArray[] = mb_strtoupper($cell->getValue());
        }

        $workPKGIndex = array_search('WORK PAKAGE NAME', $headerArray);
        $disciplineIndex = array_search('DISCIPLINE', $headerArray);
        $rows = $excel->getActiveSheet()->getRowIterator(2);
        $status = ['failed' => collect(), 'success' => 0, 'dublicated' => []];

        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row->getCellIterator());
            if (!array_filter($data)) {
                continue;
            }
//to make columns dynamic

            $activities = StdActivity::query()->pluck('code');
            if (!$activities->contains($data[$workPKGIndex + 2])) {
                $division_id = $this->getDivisionId($data, $workPKGIndex);
                $work_package_name = $data[$workPKGIndex];
                $name = $data[$workPKGIndex + 1];
                $code = $data[$workPKGIndex + 2];
                $id_partial = $data[$workPKGIndex + 3];
                $discipline = strtoupper($data[$workPKGIndex + 4]);
                $activity = StdActivity::create(['name' => $name, 'division_id' => $division_id, 'code' => $code, 'work_package_name' => $work_package_name, 'id_partial' => $id_partial, 'discipline' => $discipline]);
                if ($disciplineIndex+1 != $highest) {
                    $display_order = 1;
                    for ($i = $disciplineIndex + 1; $i < $highest; ++$i) {
                        $data[$i];
                        $label = trim($data[$i]);

                        if ($label) {
                            $activity->variables()->create(compact('label', 'display_order'));
                            ++$display_order;
                        }
                    }
                }
                ++$status['success'];
            } else {
                $status['dublicated'][] = $data[$workPKGIndex + 2];
            }

        }
        return $status;
    }

    protected function getDivisionId($data, $workPKGIndex)
    {
        if (!$this->divisions) {
            $this->divisions = collect();
            ActivityDivision::where('parent_id',0)->each(function ($division) {
                $this->divisions->put(mb_strtolower($division->canonical), $division->id);
            });

        }

        $tokens = array_filter(array_slice($data, 0, $workPKGIndex));
        $division_id = 0;
        $code = '';
        if($this->divisions->has($tokens[0])){
            $path[] = mb_strtolower($tokens[0]);
            $key = implode('/', $path);
            $this->divisions->get($key);
            unset($tokens[0]);
            foreach ($tokens as $token){
                if(strpos($token,'.')){
                    $code= substr($token,0,strpos($token,'.')+1);
                    $token= substr($token,strpos($token,'.')+1);
                }
                $path[] = mb_strtolower($token);
                $key = implode('/', $path);
                $division = ActivityDivision::create([
                    'parent_id' => $division_id,
                    'name' => $token,
                    'code'=>isset($code)?$code:'',
                ]);
                $division_id = $division->id;
                $this->divisions->put($key, $division_id);
            }
        }
        else{
            foreach ($tokens as $token) {

                if(strpos($token,'.')){
                    $code= substr($token,0,strpos($token,'.')+1);
                    $token= substr($token,strpos($token,'.')+1);
                }
                $path[] = mb_strtolower($token);
                $key = implode('/', $path);
                if ($this->divisions->has($key)) {
                    $division_id = $this->divisions->get($key);
                    continue;
                }

                $division = ActivityDivision::create([
                    'parent_id' => $division_id,
                    'name' => $token,
                    'code'=>isset($code)?$code:'',
                ]);
                $division_id = $division->id;
                $this->divisions->put($key, $division_id);
            }
        }


//        $division_id = 0;
//        $path = [];
//
//        foreach ($tokens as $token) {
//            $path[] = mb_strtolower($token);
//            $key = implode('/', $path);
//            if ($this->divisions->has($key)) {
//                $division_id = $this->divisions->get($key);
//                continue;
//            }
//
//            $division = ActivityDivision::create([
//                'parent_id' => $division_id,
//                'name' => $token,
//            ]);
//            $division_id = $division->id;
//            $this->divisions->put($key, $division_id);
//        }

        return $division_id;
    }
}
