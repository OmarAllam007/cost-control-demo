<?php

namespace App\Console\Commands;

use App\Resources;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class ExportAllResources extends Command
{
    protected $signature = 'resources:export';

    protected $description = 'Export all resources in the system';

    protected $buffer = '';

    /** @var ProgressBar */
    protected $bar;

    /** @var \PHPExcel_Worksheet */
    protected $sheet;

    /** @var int */
    protected $row = 1;

    /** @var \Illuminate\Support\Collection */
    protected $invalid_resource_types;

    public function handle()
    {
        $this->output->newLine();

        $query = Resources::with('project')->orderBy('id');

        $this->bar = $this->output->createProgressBar($query->count());
        $this->bar->setBarWidth(50);

        $this->invalid_resource_types = collect(\DB::table('resources')
            ->selectRaw('DISTINCT resource_type_id')
            ->whereRaw("resource_type_id in (select parent_id from resource_types)")
            ->get())->pluck('resource_type_id');

        $excel = new \PHPExcel();
        $this->sheet = $excel->getSheet(0);

        $this->sheet->fromArray($headers = ['APP_ID', 'Project ID', 'Project Name' ,'Code', 'Name', 'Rate', 'Unit', 'Waste', 'Reference', 'Business Partner', 'Type', 'Division 1', 'Division 2', 'Division 3', 'Division 4', 'Original Resource ID', 'Error #1', 'Error #2'], '', 'A1');

        $query->chunk(5000, function(Collection $resources) {
            $resources->each(function(Resources $resource) {
                $data = [
                    $resource->id, $resource->project_id ?: '', $resource->project->name?? '', $resource->resource_code, $resource->name,
                    $resource->rate, $resource->units->type??'', $resource->waste, $resource->reference, $resource->parteners->name ?? '',
                ];

                $parent = $resource->types;
                if ($parent) {
                    $tree = [$parent->name];
                    while ($parent = $parent->parent) {
                        $tree[] = $parent->name;
                    }
                    $tree = array_reverse($tree);
                    foreach ($tree as $item) {
                        $data[] = $item;
                    }
                }

                $data = array_pad($data, 15, '');

                $data[] = $resource->resource_id ?? '';

                if ($resource->project_id && !$resource->resource_id) {
                    $data[] = 'Resource not in database';
                }

                if (!isset($resource->types->id) || $this->invalid_resource_types->contains($resource->types->id)) {
                    $data[] = 'Invalid resource type';
                }

                ++$this->row;
                foreach ($data as $idx => $val) {
                    $column = chr(ord('A') + $idx);
                    $this->sheet->setCellValue("$column{$this->row}", $val);
                }

//                $this->sheet->fromArray($data, 0, "A{$this->row}");
                $this->bar->advance();
            });
        });
        $this->bar->finish();

        $this->output->newLine(2);
        $this->output->note('Generating Excel file');

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('storage/app/all_resources.xlsx');

        $this->output->newLine();

    }
}
