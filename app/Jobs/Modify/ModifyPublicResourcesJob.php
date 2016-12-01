<?php

namespace App\Jobs\Modify;

use App\BusinessPartner;
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

    /**
     * @var Collection
     */
    protected $partners;

    protected $resources;
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);
        $status = ['failed' => collect(), 'success' => 0];

        Resources::flushEventListeners();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            $resource = Resources::where('resource_code', $data[0])->first();
            $division_id = ResourceType::where('name',$data[2])->first();
            if (!array_filter($data)) {
                continue;
            }

            if ($resource) {
                $resource_type_id = isset($division_id)?$division_id->id:'';
                $unit_id = $this->getUnit($data[4]);
                $resource->resource_type_id = $resource_type_id;
                $resource->resource_code = $data[0];
                $resource->name = $data[1];
                $resource->rate = floatval($data[3]);
                $resource->unit = $unit_id;
                $resource->waste = $data[5];
                $resource->business_partner_id = $this->getPartner($data[7]);
                $resource->reference = $data[6];
                $resource->save();
            }

        }

        dispatch(new CacheResourcesTree());


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
