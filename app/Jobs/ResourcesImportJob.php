<?php

namespace App\Jobs;

use App\BusinessPartner;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Resources;
use App\ResourceType;
use Illuminate\Support\Collection;

class ResourcesImportJob extends ImportJob
{

    /**
     * @var
     */
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

    public function __construct($file, $project = null)
    {
        $this->file = $file;
        $this->project = $project;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $rows = $excel->getSheet(0)->getRowIterator(2);
        $status = ['failed' => collect(), 'success' => 0, 'dublicated' => [], 'project' => $this->project];

        if ($this->project) {
            $oldResources = Resources::where('project_id', $this->project->id)->get();
        } else {
            $oldResources = Resources::whereNull('project_id')->get();
        }

        $oldResourceCodes = $oldResources->map(function (Resources $resource) {
            $resource->resource_code = mb_strtolower($resource->resource_code);
            return $resource;
        })->pluck('resource_code', 'resource_code');

        Resources::flushEventListeners();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            $code = mb_strtolower($data[4]);
            if (!$oldResourceCodes->has($code)) {
                $type_id = $this->getTypeId($data);
                $unit_id = $this->getUnit($data[7]);
                $item = [
                    'resource_type_id' => $type_id,
                    'resource_code' => $data[4],
                    'name' => $data[5],
                    'rate' => floatval($data[6]),
                    'unit' => $unit_id,
                    'waste' => $this->getWaste($data[8]),
                    'business_partner_id' => $this->getPartner($data[9]),
                    'reference' => $data[10],
                    'project_id' => $this->project->id??null,
                ];
                if ($unit_id) {
                    Resources::create($item);
                    ++$status['success'];
                } else {
                    $item['orig_unit'] = $data[7];
                    $status['failed']->push($item);
                }
            } else {
                $status['dublicated'][] = $data[4];
            }
        }

        dispatch(new CacheResourcesInQueue());

        return $status;
    }

    protected function getTypeId($data)
    {
        $this->loadTypes();

        $levels = array_filter(array_slice($data, 0, 4));
        $type_id = 0;
        $path = [];
        foreach ($levels as $level) {
            $path[] = mb_strtolower($level);
            $key = implode('/', $path);

            if ($this->types->has($key)) {
                $type_id = $this->types->get($key);
            } else {
                $resource = ResourceType::create([
                    'name' => $level,
                    'parent_id' => $type_id
                ]);
                $type_id = $type_id = $resource->id;
                $this->types->put($key, $type_id);
            }
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
