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

    public function handle()
    {
        $this->output->newLine();

        $query = Resources::with('project')->orderBy('id');

        $this->bar = $this->output->createProgressBar($query->count());
        $this->bar->setBarWidth(50);

        $this->buffer .= implode(',', array_map('csv_quote', ['APP_ID', 'Project ID', 'Project Name' ,'Code', 'Name', 'Rate', 'Unit', 'Waste', 'Reference', 'Business Partner', 'Type', 'Subtype', 'Sub Subtype', '...']));

        $query->chunk(2000, function(Collection $resources) {
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

                $data[] = $resource->resource_id;

                $this->buffer .= PHP_EOL . implode(',', array_map('csv_quote', $data));
                $this->bar->advance();
            });
        });

        file_put_contents('storage/app/all_resources.csv', $this->buffer);

        $this->bar->finish();
        $this->output->newLine();
    }
}
