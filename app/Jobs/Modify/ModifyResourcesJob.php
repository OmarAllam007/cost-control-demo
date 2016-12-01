<?php

namespace App\Jobs\Modify;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\Project;
use App\Resources;
use App\ResourceType;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyResourcesJob extends ImportJob
{
    protected $file;

    protected $project;
    protected $partners;

    public function __construct($project, $file)
    {
        $this->file = $file;
        $this->project = $project;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);


        Resources::flushEventListeners();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }
            $resource = Resources::where('id', $data[9])->where('project_id', $this->project->id)->first();
            $type_id = $this->getTypeId($data[2]);
            $unit_id = $this->getUnit($data[4]);
            if ($resource->resource_id) {
                $resource->resource_type_id = $type_id;
                $resource->resource_code = $data[0];
                $resource->name = $data[1];
                $resource->rate = $data[3];
                $resource->unit = $unit_id;
                $resource->waste = $this->getWaste($data[5]);
                $resource->business_partner_id = $this->getPartner($data[7]);
                $resource->reference = $data[6];
                $resource->update();
                BreakdownResource::where('resource_id',$resource->id)->update(['resource_id'=>$resource->resource_id]);
            } else {
                $item = [
                    'resource_type_id' => $type_id,
                    'resource_code' => $data[0],
                    'name' => $data[1],
                    'rate' => floatval($data[3]),
                    'unit' => $unit_id,
                    'waste' => $this->getWaste($data[5]),
                    'business_partner_id' => $this->getPartner($data[7]),
                    'reference' => $data[6],
                    'project_id'=>$resource->project_id,
                    'resource_id' => $resource->id,
                ];
                $newResource = $resource->create($item);
                $resource->resource_id = $newResource->id;
                BreakdownResource::where('resource_id',$resource->id)->update(['resource_id'=>$newResource->id]);
            }
        }

    }

    protected function getTypeId($type)
    {
        $type_id = ResourceType::where('name', $type)->first();
        if ($type_id->id) {
            return $type_id->id;
        }
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
