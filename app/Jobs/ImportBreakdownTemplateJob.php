<?php

namespace App\Jobs;

use App\BreakdownTemplate;
use App\Productivity;
use App\Resources;
use App\StdActivity;
use Illuminate\Support\Collection;

class ImportBreakdownTemplateJob extends ImportJob
{
    protected $file;
    protected $count;
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
        $this->file = $file;
        $this->loadActivities();
        $this->loadResources();
        $this->templates = collect();
        $this->loadTemplates();
    }

    function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $loader->setReadDataOnly(true);
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $this->count = 0;
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
            $resource_id = $this->getResource(trim($data[3]));
            if ($resource_id) {
                $template->resources()->create([
                    'resource_id' => $resource_id,
                    'equation' => isset($data[5]) ? $data[5] : '',
                    'labor_count' => isset($data[6]) ? $data[6] : '',
                    'productivity_id' => $data[7] ? $this->getProductivity($data[7]) : 0,
                    'remarks' => isset($data[8]) ? $data[8] : ''
                ]);
            }
        }

        return $this->count;
    }

    protected function loadActivities()
    {
        $this->activities = StdActivity::all()->keyBy(function ($activity) {
            return $activity->code;
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
            $template = $activity->breakdowns()->create(compact('name'));
            $this->count++;
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

        Resources::select(['resource_code', 'id'])->whereNull('project_id')->get()->each(function (Resources $resource) {
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

    protected function loadTemplates(){
        BreakdownTemplate::whereNull('project_id')->each(function (BreakdownTemplate $template){
            $this->templates->push($template->code);
        });
    }
}