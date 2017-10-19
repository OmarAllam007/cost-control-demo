<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\MasterShadow;
use App\Resources;
use App\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;

class UpdateResourceTypesAndResources extends Command
{
    protected $signature = 'update-resources';

    /** @var \PHPExcel */
    protected $excel;

    /** @var \Illuminate\Support\Collection */
    protected $typesCache;

    /** @var \Illuminate\Support\Collection */
    protected $resourceTypeMap;
    protected $divisionsCache;


    function __construct()
    {
        parent::__construct();



        $this->typesCache = collect();
        $this->resourceTypeMap = collect();
    }


    function handle()
    {
        ResourceType::unguard();
        ResourceType::flushEventListeners();

        Resources::unguard();
        Resources::flushEventListeners();

        $this->excel = \PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('misc/resources-data.xlsx'));

        $this->updateRootTypes();

        $this->updateResourceTypes();

        $this->updateResources();

        $this->updateBudgetShadow();

        $this->updateMasterShadow();
    }

    protected function updateRootTypes()
    {
        ResourceType::truncate();
        $this->output->title('Creating root resource types');

        $rootTypes = [
            '01' => ['name' => '01.General Requirment', 'code' => 'G'],
            '02' => ['name' => '02.Labors', 'code' => 'L'],
            '03' => ['name' => '03.MATERIAL', 'code' => 'M'],
            '04' => ['name' => '04.Subcontractors', 'code' => 'S'],
            '05' => ['name' => '05.EQUIPMENT', 'code' => 'E'],
            '06' => ['name' => '06.SCAFFOLDING', 'code' => 'F'],
            '07' => ['name' => '07.OTHERS', 'code' => 'O'],
            '08' => ['name' => '08.Management Reserve', 'code' => 'R'],
        ];

        foreach ($rootTypes as $ref => $attributes) {
            $type = new ResourceType();
            $type->name = $attributes['name'];
            $type->code = $attributes['code'];
            $type->parent_id = 0;
            $type->save();
            $key =$attributes['code'] . $attributes['name'];
            $this->typesCache->put($key, $type);
        }

        $this->output->success('Root resource types created');
        $this->output->newLine();
    }

    protected function updateResourceTypes()
    {
        $this->output->title('Updating child resource types');

        $sheet = $this->excel->getSheetByName('Structure');

        $rowIterator = $sheet->getRowIterator(2);

        $count = $sheet->getHighestRow(0) - 1;
        $bar = $this->output->createProgressBar($count);
        foreach ($rowIterator as $row) {
            $cellsIterator = $row->getCellIterator();
            $cells = $this->getCellsFromIterator($cellsIterator);
            $data = array_filter(array_map('trim', array_slice($cells, 1, 5)));
            $code = $cells[0];
            $codeTokens = explode('.', $code);

            $parent = null;
            $codeBuffer = [];
            // Creates types tree by accumulating code parts
            foreach ($data as $index => $name) {
                $codeBuffer[] = $codeTokens[$index];
                $typeCode = implode('.', $codeBuffer);
                $key = $typeCode . $name;

                // If this is the first iteration, which means it is a root type,
                // or we already have this type in cache, then get it from cache
                if (!$parent || $this->typesCache->has($key)) {
                    $parent = $this->typesCache->get($key);
                } else {
                    $parent = ResourceType::create(['name' => $name, 'code' => $typeCode, 'parent_id' => $parent->id ?? 0]);
                    $this->typesCache->put($key, $parent);
                }
            }
            $bar->advance();
        }

        $bar->finish();

        $this->output->newLine(2);

        $this->output->success('Resource types created');

        $this->output->newLine();
    }

    protected function updateResources()
    {
        $this->output->title('Updating Resources');

        $sheet = $this->excel->getSheetByName('Modified Ordered');

        $count = $sheet->getHighestRow(0) - 1;
        $bar = $this->output->createProgressBar($count);

        $rowIterator = $sheet->getRowIterator(2);

        foreach ($rowIterator as $row) {
            $cellsIterator = $row->getCellIterator();
            $data = $this->getCellsFromIterator($cellsIterator);

            $id = trim($data[0]);
            $name = trim($data[4]);
            $resource_code = trim($data[3]);

            $typeNames = array_filter(array_map('trim', array_slice($data, 10, 5)));
            $parent_id = 0;
            $types = [];
            foreach ($typeNames as $typeName) {
                $type =  ResourceType::where('parent_id', $parent_id)->where('name', $typeName)->first();

                if (!$type) {
                    dd($parent_id, $typeName, $typeNames, $types);
                }
                $types[] = $type;
                $parent_id = $type->id;
            }


            $resource_type_id = $parent_id;

            $resource= Resources::find($id);
            if (!$resource) {
                dd($id);
            }
            $resource->update(compact('name', 'resource_code', 'resource_type_id'));

            Resources::where('resource_id', $id)->update(compact('resource_code', 'resource_type_id'));

            $bar->advance();
        }

        $bar->finish();

        $this->output->newLine(2);

        $this->output->success('Resources updated');

        $this->output->newLine();
    }

    protected function getCellsFromIterator(\PHPExcel_Worksheet_CellIterator $cellsIterator)
    {
        $data = [];

//        $cellsIterator->setIterateOnlyExistingCells(true);

        foreach ($cellsIterator as $cell) {
            $data[] = $cell->getValue();
        }

        return $data;
    }

    protected function updateBudgetShadow()
    {
        $this->output->title('Updating budget shadow');

        $count = BreakDownResourceShadow::count();
        $bar = $this->output->createProgressBar($count);

        BreakDownResourceShadow::flushEventListeners();
        BreakDownResourceShadow::with('resource')->chunk(20000, function($shadows) use ($bar) {
            foreach ($shadows as $shadow) {
                if (isset($shadow->resource->types->root)) {
                    $root = $shadow->resource->types->root;
                    $shadow->resource_type_id = $root->id;
                    $shadow->resource_type = $root->name;
                    $shadow->resource_code = $shadow->resource->resource_code;
                    $shadow->save();
                }

                $bar->advance();
            }
        });

        $bar->finish();

        $this->output->newLine(2);
        $this->output->success('Budget shadow updated');
        $this->output->newLine();
    }

    protected function updateMasterShadow()
    {
        $this->output->title('Updating master shadow');

        $count = MasterShadow::count();
        $bar = $this->output->createProgressBar($count);

        MasterShadow::with('resource')->chunk(5000, function($shadows) use ($bar) {
            foreach ($shadows as $shadow) {
                if (isset($shadow->resource->types->root)) {
                    $root = $shadow->resource->types->root;

                    $shadow->resource_type_id = $root->id;
                    $shadow->resource_divs = $this->getResourceDivisions($shadow->resource);
                    $shadow->resource_code = $shadow->resource->resource_code;

                    $shadow->save();
                }

                $bar->advance();
            }
        });

        $bar->finish();

        $this->output->newLine(2);
        $this->output->success('Master shadow updated');
        $this->output->newLine();
    }

    protected function getResourceDivisions($resource)
    {
        if (isset($this->divisionsCache['divisions'][$resource->resource_type_id])) {
            return $this->divisionsCache['divisions'][$resource->resource_type_id];
        }

        $parent = $division = $resource->types;
        $divisions = [$division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->name;
        }

        return $this->divisionsCache['divisions'][$resource->resource_type_id] = array_reverse($divisions);
    }
}
