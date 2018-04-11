<?php

namespace App\Console\Commands;

use App\ActualResources;
use App\StoreResource;
use Illuminate\Console\Command;
use function is_object;
use function json_decode;
use Symfony\Component\Console\Helper\ProgressBar;

class MigrateActualResources extends Command
{
    protected $signature = 'migrate_actual_resources';

    /** @var ProgressBar */
    private $bar;

    public function handle()
    {
        $count = ActualResources::count();

        $this->bar = $this->output->createProgressBar($count);
        $this->bar->setBarWidth(60);

        StoreResource::truncate();

        ActualResources::with('breakdown_resource')->chunk(1000, function ($group) {
            foreach ($group as $resource) {
                $original_data = json_decode($resource->original_data, true);
                if (empty($original_data)) {
                    $this->bar->advance();
                    continue;
                }

                if (!$resource->breakdown_resource) {
                    continue;
                }


                StoreResource::create([
                    'project_id' => $resource->project_id,
                    'period_id' => $resource->period_id,
                    'batch_id' => $resource->batch_id,
                    'budget_code' => $resource->breakdown_resource->code,
                    'resource_id' => $resource->breakdown_resource->resource_id,
                    'activity_code' => $original_data[0],
                    'store_date' => $original_data[1],
                    'item_desc' => $original_data[2],
                    'measure_unit' => $original_data[3],
                    'qty' => $original_data[4],
                    'unit_price' => $original_data[5],
                    'cost' => $original_data[6],
                    'item_code' => $original_data[7],
                    'doc_no' => $original_data[8] ?? '',
                    'created_at' => $resource->created_at,
                ]);

                $this->bar->advance();
            }
        });

        $this->bar->finish();
        $this->output->newLine();
    }
}
