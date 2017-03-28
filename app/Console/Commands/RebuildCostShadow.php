<?php

namespace App\Console\Commands;

use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\WbsResource;
use Illuminate\Console\Command;

class RebuildCostShadow extends Command
{
    protected $signature = 'cost:rebuild-shadow';

    protected $description = 'Rebuild shadow for cost data';

    public function handle()
    {
        $resources = ActualResources::get(['breakdown_resource_id', 'period_id']);
        $bar = $this->output->createProgressBar($resources->count());
        $bar->setBarWidth(80);

        CostShadow::truncate();

        WbsResource::unguard();

        $fields = [
            "project_id", "wbs_level_id", "period_id", "resource_id", "breakdown_resource_id", "curr_cost", "curr_qty",
            "curr_unit_price", "prev_cost", "prev_qty", "to_date_cost", "to_date_qty", "prev_unit_price",
            "to_date_unit_price", "progress", "allowable_ev_cost", "allowable_var", "bl_allowable_cost", "bl_allowable_var",
            "remaining_qty", "remaining_cost", "remaining_unit_price", "completion_qty", "completion_cost", "completion_unit_price",
            "qty_var", "cost_var", "unit_price_var", "physical_unit", "pw_index", "cost_variance_to_date_due_unit_price",
            "allowable_qty", "cost_variance_remaining_due_unit_price", "cost_variance_completion_due_unit_price",
            "cost_variance_completion_due_qty", "cost_variance_to_date_due_qty", 'batch_id', 'doc_no', 'budget_unit_rate'
        ];

        foreach ($resources as $resource) {
            $resource = $resource->toArray();
            $data = \DB::select('
SELECT project_id, wbs_level_id, breakdown_resource_id, period_id, resource_id, 
  sum(qty) AS curr_qty, sum(cost) AS curr_cost, CASE WHEN sum(qty) != 0 THEN sum(cost) / sum(qty) ELSE 0 END AS curr_unit_price
FROM actual_resources 
WHERE breakdown_resource_id = :breakdown_resource_id AND period_id = :period_id 
GROUP BY 1, 2, 3, 4, 5', $resource);
            $data = (array) $data[0];

            $previousData = \DB::select('
SELECT project_id, wbs_level_id, breakdown_resource_id, period_id, resource_id, 
sum(qty) AS prev_qty, sum(cost) AS prev_cost, CASE WHEN sum(qty) != 0 THEN sum(cost) / sum(qty) ELSE 0 END AS prev_unit_price
FROM actual_resources 
WHERE breakdown_resource_id = :breakdown_resource_id AND period_id < :period_id 
GROUP BY 1, 2, 3, 4, 5', $resource);

            if (empty($previousData)) {
                $previousData = ['prev_qty' => 0, 'prev_cost' => 0, 'prev_unit_price' => 0];
            } else {
                $previousData = (array) $previousData[0];
            }

            $data['prev_qty'] = $previousData['prev_qty'];
            $data['prev_cost'] = $previousData['prev_cost'];
            $data['prev_unit_price'] = $previousData['prev_unit_price'];

            $data['to_date_qty'] = $data['curr_qty'] + $previousData['prev_qty'];
            $data['to_date_cost'] = $data['curr_cost'] + $previousData['prev_cost'];
            if ($data['to_date_qty']) {
                $data['to_date_unit_price'] = $data['to_date_cost'] / $data['to_date_qty'];
            } else {
                $data['to_date_unit_price'] = 0;
            }

            $shadow = BreakDownResourceShadow::where('breakdown_resource_id', $resource['breakdown_resource_id'])->first();
            /** @var $shadow BreakDownResourceShadow */
            $data = array_merge($data, $shadow->toArray());


            $wbs_resource = new WbsResource($data);
            $wbs_resource->appendFields();
            $insert = collect($wbs_resource->toArray())->only($fields);
            if (!CostShadow::where($resource)->exists()) {
                CostShadow::create($insert->toArray());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }
}
