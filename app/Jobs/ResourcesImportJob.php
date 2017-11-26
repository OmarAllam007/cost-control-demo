<?php

namespace App\Jobs;

use App\BusinessPartner;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Resources;
use App\ResourceType;
use Illuminate\Support\Collection;

class ResourcesImportJob extends ImportJob
{

    /** @var string */
    protected $file;

    /** @var Collection */
    protected $types;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $partners;

    /** @var int */
    protected $project_id;

    /** @var Collection */
    protected $oldResourceNames;

    /** @var Collection */
    protected $oldResourceCodes;

    public function __construct($file, $project = null)
    {
        $this->file = $file;
        $this->project = $project;
        $this->project_id = $project ? $project->id : null;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $rows = $excel->getSheet(0)->getRowIterator(2);
        $status = ['units' => collect(), 'success' => 0, 'project' => $this->project];

        $this->loadOldResources();

        $resultRows = collect();
        $rules = [
            'name' => 'required', 'resource_type_id' => 'required|no_resource_on_parent', 'unit' => 'required',
            'rate' => 'required|gte:0', 'waste' => 'gte:0|lt:100'
        ];

        foreach ($rows as $index => $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            $code = mb_strtolower($data[6]);
            $name = str_replace(' ', '', mb_strtolower($data[7]));

            if ($this->oldResourceCodes->has($code)) {
                $data[13] = 'Duplicated Code';
                $resultRows->push($data);
            } elseif ($this->oldResourceNames->has($name)) {
                $data[13] = 'Duplicated name';
                $resultRows->push($data);
            } else {
                $type_id = $this->getTypeId($data);
                $unit_id = $this->getUnit($data[9]);

                $item = ['resource_type_id' => $type_id, 'name' => $data[7],
                    'rate' => floatval($data[8]), 'unit' => $unit_id, 'waste' => $this->getWaste($data[10]),
                    'business_partner_id' => $this->getPartner($data[11]), 'reference' => $data[12],
                    'project_id' => $this->project_id];

                if (!$unit_id) {
                    $item['orig_unit'] = $data[9];
                    $status['units']->push($item);

                    continue;
                }

                $validator = \Validator::make($item, $rules);
                if ($validator->passes()) {
                    $resource = Resources::create($item);
                    $data[13] = '';
                    $data[6] = $resource->resource_code;
                    $resultRows->push($data);
                    ++$status['success'];
                } else {
                    $data[13] = implode("\n", $validator->errors()->all());
                    $resultRows->push($data);
                }
            }
        }

        $status['result_file'] = $this->createResultExcel($resultRows);

        dispatch(new CacheResourcesInQueue());

        return $status;
    }

    protected function getTypeId($data)
    {
        $this->loadTypes();

        $levels = array_filter(array_slice($data, 0, 6));

        $type_id = 0;
        $path = [];
        foreach ($levels as $level) {
            $path[] = trim(strtolower($level));
            $key = implode('/', $path);

            if ($this->types->has($key)) {
                $type_id = $this->types->get($key);
//            } else {
//                $resource = ResourceType::create([
//                    'name' => $level,
//                    'parent_id' => $type_id
//                ]);
//                $type_id = $type_id = $resource->id;
//                $this->types->put($key, $type_id);
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

    protected function loadOldResources()
    {
        $oldResources = Resources::whereNull('project_id')->get();

        $this->oldResourceCodes = $oldResources->map(function (Resources $resource) {
            $resource->resource_code = mb_strtolower($resource->resource_code);
            return $resource;
        })->pluck('resource_code', 'resource_code');

        $this->oldResourceNames = $oldResources->map(function (Resources $resource) {
            $resource->encoded_name = mb_strtolower(str_replace(' ', '', $resource->name));
            return $resource;
        })->pluck('encoded_name', 'encoded_name');
    }

    protected function createResultExcel(Collection $result_rows)
    {
        if ($result_rows->isEmpty()) {
            return '';
        }

        $excel = new \PHPExcel();

        $sheet = $excel->getSheet(0);

        $sheet->setTitle('Failed resources');

        $sheet->fromArray(["RESOURCE TYPE", "RESOURCE DIVISION", "RESOURCE SUB DIVISION 1", "RESOURCE SUB DIVISION 2",
            "RESOURCE SUB DIVISION 3", "RESOURCE SUB DIVISION 4", "RESOURCE CODE", "RESOURCE NAME", "STANDARD RATE",
            "UNIT OF MEASURE", "MATERIAL Waste %",	"SUPPLIER/ SUBCON.", "REFERENCE", "Errors"]);

        $result_rows->each(function($row, $counter) use ($sheet) {
            $row_num = $counter + 2;
            $sheet->fromArray($row, '', "A{$row_num}", true);
        });

        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $filename = uniqid('import_resources_') . '.xlsx';
        $filepath = storage_path('app/public/' . $filename);
        $writer->save($filepath);

        return '/storage/' . $filename;
    }
}
