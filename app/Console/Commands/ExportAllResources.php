<?php

namespace App\Console\Commands;

use App\Resources;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class ExportAllResources extends Command
{
    protected $signature = 'resources:export';

    protected $description = 'Export all resources in the system';

    protected $buffer = '';

    public function handle()
    {
        $this->buffer .= array_map('csv_quote', ['APP_ID', 'Code', 'Name', 'Rate', 'Unit', 'Waste', 'Reference', 'Business Partner']);
        Resources::with('project')->orderByRaw('project nulls first')->chunk(1000, function(Collection $resources) {
            $resources->each(function(Resources $resource) {
                $this->buffer .= PHP_EOL . implode(',', array_map('csv_quote', [

                ]));
            });
        });
    }
}
