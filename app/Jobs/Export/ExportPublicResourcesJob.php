<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Resources;
use App\ResourceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ExportPublicResourcesJob extends Job
{
    /** @var int */
    protected $row = 2;

    /** @var Collection */
    protected $types;

    public function handle()
    {
        $this->loadTypes();

        $excel = \PHPExcel_IOFactory::load(public_path('files/templates/resources.xlsx'));

        $sheet = $excel->getSheet(0);

        Resources::whereNull('project_id')->with('units')
            ->chunk(3000, function (Collection $resources) use ($sheet) {
                $missingType = array_fill(0, 6, '');

                /** @var Resources $resource */
                foreach ($resources as $resource) {
                    $types = $this->types->get($resource->resource_type_id, $missingType);
                    $rate = number_format(floatval($resource->rate), 2, '.', '');
                    $unit = $resource->units->type ?? '';
                    $waste = number_format(floatval($resource->waste), 2, '.', '');
                    $supplier = $resource->parteners->name ?? '';

                    $resource_data = [
                        $resource->resource_code, $resource->name, $rate, $unit, $waste, $supplier, $resource->reference
                    ];

                    $data = array_merge($types, $resource_data);

                    $sheet->fromArray($data, '', "A{$this->row}");

                    ++$this->row;
                }
            });

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $filename = storage_path('app/' . uniqid() . '.xlsx');
        $writer->save($filename);

        return \Response::download($filename, 'KPS_Resources.xlsx')->deleteFileAfterSend(true);
    }

    protected function loadTypes()
    {
        $this->types = ResourceType::with('parent.parent.parent.parent.parent')
            ->whereRaw('id in (select resource_type_id from resources)')
            ->get()->map(function (ResourceType $type) {
                $tree = [$type->name];

                $parent = $type->parent;
                while ($parent) {
                    $tree[] = $parent->name;
                    $parent = $parent->parent;
                }

                $tree = array_pad(array_reverse($tree), 6, '');

                $type->tree = $tree;
                return $type;
            })->pluck('tree', 'id');

//        dd($this->types);
    }
}
