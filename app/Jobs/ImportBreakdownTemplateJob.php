<?php

namespace App\Jobs;

use App\Productivity;
use App\Resources;
use App\StdActivity;
use Illuminate\Support\Collection;

class ImportBreakdownTemplateJob extends ImportJob
{
    protected $file;
    /**
     * @var Collection
     */
    protected $activities;

    /**
     * @var Collection
     */
    protected $templates;

    /**
     * @var Collection
     */
    protected $resources;

    function __construct($file)
    {
        ini_set('memory_limit', '256m');
        set_time_limit(300);

        $this->file = $file;
        $this->loadActivities();
        $this->loadResources();
        $this->templates = collect();
    }

    function handle()
    {
        ini_set('max_execution_time', 300);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $activity = $this->getActivity($data[0]);
            if (!$activity) {
                continue;
            }

            $template = $this->getTemplate($data[2], $activity);
            if (!$template) {
                continue;
            }

            $resource_id = $this->getResource($data[3]);

            if ($resource_id) {
                $resource = $template->resources()->create([
                    'resource_id' => $resource_id,
                    'equation' => isset($data[5])?$data[5]:'',
                    'labor_count' => isset($data[6])?$data[6]:'',
                    'productivity_id' => $data[7]? $this->getProductivity($data[7]) : 0,
                    'remarks' => isset($data[8])?$data[8]:''
                ]);
            }
        }

        return true;
    }

    protected function loadActivities()
    {
        $this->activities = StdActivity::all()->keyBy(function($activity){
            return strtolower(strval($activity->code));
        });
    }

    protected function getActivity($code)
    {
        return $this->activities->get(strval($code)) ?: null;
    }

    protected function getTemplate($name, StdActivity $activity)
    {
        $key = strval($activity->id . '.' . strtolower($name));
        if (!$this->templates->has($key)) {
            $template = $activity->breakdowns()->firstOrCreate(compact('name'));
            $this->templates->put($key, $template);
        }

        return $this->templates->get($key);
    }

    protected function getResource($code)
    {
        $code = strtolower($code);
        return $this->resources->get($code) ?: 0;
    }

    protected function loadResources()
    {
        $this->resources = collect();

        Resources::select(['resource_code', 'id'])->get()->each(function (Resources $resource) {
            $this->resources->put(strtolower($resource->resource_code), $resource->id);
        });
    }

    protected function getProductivity($ref)
    {
        $productivity = Productivity::where('csi_code', $ref)->first();
        if ($productivity) {
            return $productivity->id;
        }
        return 0;
    }
}