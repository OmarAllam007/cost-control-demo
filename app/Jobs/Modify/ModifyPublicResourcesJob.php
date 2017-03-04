<?php

namespace App\Jobs\Modify;

use App\BusinessPartner;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Jobs\CacheResourcesInQueue;
use App\Jobs\CacheResourcesTree;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\Resources;
use App\ResourceType;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyPublicResourcesJob extends ImportJob
{

    protected $file;

    /**
     * @var Collection
     */
    protected $types;
    protected $project;

    /**
     * @var Collection
     */
    protected $partners;

    protected $resources;

    public function __construct($file, $project)
    {
        $this->file = $file;
        $this->project = $project;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);
        $status = ['failed' => collect(), 'success' => 0];

        Resources::flushEventListeners();

        $query = Resources::query();
        if ($this->project) {
            $query->where('project_id', $this->project);
        }

        $resources = $query->get()->map(function ($resource) {
            $resource->resource_code = mb_strtolower($resource->resource_code);
            return $resource;
        })->keyBy('resource_code');

        /*$divisions = ResourceType::all()->map(function ($type) {
            $type->name = mb_strtolower($type->name);
            return $type;
        })->pluck('id','name');*/

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            $code = mb_strtolower($data[0]);
            /** @var Resources $resource */
            $resource = $resources->get($code);
            if (!$resource) {
                continue;
            }

            /*if ($type_id = $divisions->get(mb_strtolower('type_name'))) {
                $resource->resource_type_id = $type_id;
            }*/

            $resource->resource_code = $data[0];
            $resource->name = $data[1];
            $resource->rate = floatval($data[3]);
            $resource->unit = $this->getUnit($data[4]);
            $resource->waste = $data[5];
            $resource->business_partner_id = $this->getPartner($data[7]);
            $resource->reference = $data[6];
            if ($this->project) {
                $resource->project_id = $this->project;
            }
            $resource->save();
            $resource->updateBreakdownResources();
        }

        dispatch(new CacheResourcesInQueue());
        return $status;
    }

    protected function getTypeId($data)
    {
        $this->loadTypes();

//        $levels = array_filter(array_slice($data, 0, 4));
        $type_id = 0;
        $path = [];

        $path[] = mb_strtolower($data);
        $key = implode('/', $path);

        if ($this->types->has($key)) {
            $type_id = $this->types->get($key);
        } else {
            $resource = ResourceType::create([
                'name' => $data,
                'parent_id' => $type_id
            ]);
            $type_id = $type_id = $resource->id;
            $this->types->put($key, $type_id);
        }


        return $type_id;
    }

    protected function getWaste($waste)
    {
        $waste = floatval($waste);
        return $waste < 1 ? $waste * 100 : $waste;
    }

    protected function getPartner($partner)
    {
        if (!$this->partners) {
            $this->partners = collect();
            BusinessPartner::all()->each(function ($partner) {
                $this->partners->put(mb_strtolower($partner->name), $partner->id);
            });
        }

        if (!$partner) {
            return 0;
        }

        $key = mb_strtolower($partner);
        if ($this->partners->has($key)) {
            return $this->partners->get($key);
        }

        $partnerObject = BusinessPartner::create(['name' => $partner]);
        $this->partners->put(mb_strtolower($partner), $partnerObject->id);
        return $partnerObject->id;
    }

    private function loadTypes()
    {
        if ($this->types) {
            return $this->types;
        }

        $this->types = collect();
        ResourceType::all()->each(function ($type) {
            $this->types->put(mb_strtolower($type->canonical), $type->id);
        });

        return $this->types;
    }

}
