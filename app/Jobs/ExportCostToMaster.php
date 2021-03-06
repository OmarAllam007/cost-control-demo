<?php

namespace App\Jobs;

use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Job;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivity;
use App\WasteIndex;
use App\WbsLevel;
use App\WbsResource;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use function microtime;
use function strtolower;

class ExportCostToMaster extends Job implements ShouldQueue
{
    use SerializesModels, InteractsWithQueue;

    protected $project;

    /** @var Collection */
    protected $lines;

    protected $buffer = '';

    protected $cache = ['activity' => [], 'wbs' => []];
    /**
     * @var bool
     */
    private $export;
    /**
     * @var Period
     */
    private $period;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }


    public function handle()
    {
        MasterShadow::where('period_id', $this->period->id)->delete();

        BreakDownResourceShadow::where('project_id', $this->project->id)->where('show_in_cost', 1)
            ->chunk(800, function ($shadows) {
                $start = microtime(1);
                $records = [];
                $now = Carbon::now()->format('Y-m-d H:i:s');
                foreach ($shadows as $costShadow) {
                    $costShadow->setCalculationPeriod($this->period);
                    $boq = Boq::find($costShadow->boq_id);
                    if ($boq) {
                        $boqDescription = $boq->description;
                        $boqDiscipline = $boq->type;
                        $boq_id = $boq->id;
                        $boq_wbs_id = $boq->wbs_id;
                    } else {
                        $boqDescription = '';
                        $boqDiscipline = '';
                        $boq_id = 0;
                        $boq_wbs_id = 0;
                    }

                    if (isset($this->cache['resources'][$costShadow->resource_id])) {
                        $resource = $this->cache['resources'][$costShadow->resource_id];
                    } else {
                        $this->cache['resources'][$costShadow->resource_id] = $resource = Resources::find($costShadow->resource_id);
                    }

                    $wbs = $this->getWbs($costShadow);
                    $activityDivs = $this->getActivityDivisions($costShadow);

                    $records[] = [
                        'budget_id' => $costShadow['id'], 'project_id' => $this->project->id, 'period_id' => $this->period->id,
                        'breakdown_resource_id' => $costShadow['breakdown_resource_id'],
                        'wbs_id' => $costShadow->wbs->id, 'activity_id' => $costShadow['activity_id'],
                        'resource_id' => $costShadow['resource_id'], 'resource_type_id' => $costShadow['resource_type_id'],
                        'productivity_id' => $costShadow['productivity_id'],
                        'wbs' => json_encode($wbs), 'activity_divs' => json_encode($activityDivs), 'activity' => $costShadow['activity'],
                        'code' => $costShadow['code'], 'boq' => $boqDescription, 'cost_account' => $costShadow['cost_account'],
                        'eng_qty' => $costShadow['eng_qty'], 'budget_qty' => $costShadow['budget_qty'],
                        'resource_qty' => $costShadow['resource_qty'], 'waste' => $costShadow['resource_waste'],
                        'resource_divs' => json_encode($this->getResourceDivisions($resource)),
                        'resource_code' => $costShadow['resource_code'],
                        'resource_name' => $costShadow['resource_name'], 'top_material' => $resource->top_material ?? 0,
                        'unit_price' => $costShadow['unit_price'], 'measure_unit' => $costShadow['measure_unit'],
                        'budget_unit' => $costShadow['budget_unit'], 'budget_cost' => $costShadow['budget_cost'],
                        'boq_equivilant_rate' => $costShadow['boq_equivilant_rate'], 'budget_unit_rate' => $costShadow['budget_unit_rate'],
                        'labors_count' => $costShadow['labors_count'], 'productivity_output' => $costShadow['productivity_output'],
                        'productivity_ref' => $costShadow['productivity_ref'], 'remarks' => $costShadow['remarks'],
                        'progress' => $costShadow['progress'], 'status' => $costShadow['status'] ?: 'Not Started',
                        'prev_unit_price' => $costShadow['prev_unit_price'], 'prev_qty' => $costShadow['prev_qty'],
                        'prev_cost' => $costShadow['prev_cost'], 'curr_unit_price' => $costShadow['curr_unit_price'],
                        'curr_qty' => $costShadow['curr_qty'], 'curr_cost' => $costShadow['curr_cost'],
                        'to_date_unit_price' => $costShadow['to_date_unit_price'], 'to_date_qty' => $costShadow['to_date_qty'],
                        'to_date_cost' => $costShadow['to_date_cost'], 'allowable_ev_cost' => $costShadow['allowable_ev_cost'],
                        'allowable_var' => $costShadow['allowable_var'], 'remaining_unit_price' => $costShadow['remaining_unit_price'],
                        'remaining_qty' => $costShadow['remaining_qty'], 'remaining_cost' => $costShadow['remaining_cost'],
                        'bl_allowable_cost' => $costShadow['bl_allowable_cost'], 'bl_allowable_var' => $costShadow['bl_allowable_var'],
                        'completion_unit_price' => $costShadow['completion_unit_price'], 'completion_qty' => $costShadow['completion_qty'],
                        'completion_cost' => $costShadow['completion_cost'], 'unit_price_var' => $costShadow['unit_price_var'],
                        'qty_var' => $costShadow['qty_var'], 'cost_var' => $costShadow['cost_var'], 'physical_unit' => $costShadow['physical_unit'],
                        'cost_variance_to_date_due_unit_price' => $costShadow['cost_variance_to_date_due_unit_price'], 'allowable_qty' => $costShadow['allowable_qty'],
                        'cost_variance_remaining_due_unit_price' => $costShadow['cost_variance_remaining_due_unit_price'],
                        'cost_variance_completion_due_unit_price' => $costShadow['cost_variance_completion_due_unit_price'],
                        'cost_variance_completion_due_qty' => $costShadow['cost_variance_completion_due_qty'],
                        'cost_variance_to_date_due_qty' => $costShadow['cost_variance_to_date_due_qty'],
                        'boq_discipline' => $boqDiscipline, 'boq_id' => $boq_id, 'boq_wbs_id' => $boq_wbs_id,
                        'to_date_price_var' => $costShadow['to_date_price_var'], 'to_date_qty_var' => $costShadow['to_date_qty_var'],
                        'created_at' => $now, 'updated_at' => $now,
                        'completion_cost_optimistic' => $costShadow['completion_cost_optimistic'],
                        'completion_cost_likely' => $costShadow['completion_cost_likely'],
                        'completion_cost_pessimistic' => $costShadow['completion_cost_pessimistic'],
                        'completion_var_optimistic' => $costShadow['completion_var_optimistic'],
                        'completion_var_likely' => $costShadow['completion_var_likely'],
                        'completion_var_pessimistic' => $costShadow['completion_var_pessimistic'],
                        //'pw_index' => $costShadow['pw_index']
                    ];
                }

                \DB::transaction(function () use ($records) {
                    MasterShadow::insert($records);
                });

                unset($shadows, $records);
                gc_collect_cycles();

                $time = microtime(true) - $start;
                \Log::info("Chunk has been buffered; project: {$this->project->id} memory ({$this->project->id}): " . round(memory_get_usage() / (1024 * 1024), 2) . ', Time: ' . round($time, 4));
            });

        $this->generateWasteIndex();

        $this->period->update(['status' => Period::GENERATED]);
    }

    protected function getWbs($costShadow)
    {
        $level = $costShadow->wbs;
        if (isset($this->cache['wbs'][$level->id])) {
            return $this->cache['wbs'][$level->id];
        }

        $wbsLevels = [$level->name];
        $parent = $level;
        while ($parent = $parent->parent) {
            $wbsLevels[] = $parent->name;
        }

        $wbsLevels = array_reverse($wbsLevels);
        return $this->cache['wbs'][$level->id] = $wbsLevels;
    }

    protected function getActivityDivisions($costShadow)
    {
        if (isset($this->cache['activity'][$costShadow->activity_id])) {
            return $this->cache['activity'][$costShadow->activity_id];
        }

        $activity = StdActivity::find($costShadow->activity_id);
        $division = $parent = $activity->division;
        $divisions = [$division->code . ' ' . $division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->code . ' ' . $parent->name;
        }

        return $this->cache['activity'][$costShadow->activity_id] = array_reverse($divisions);
    }

    protected function getResourceDivisions($resource)
    {
        if (!$resource) {
            return [];
        }

        if (isset($this->cache['divisions'][$resource->id])) {
            return $this->cache['divisions'][$resource->id];
        }

        $parent = $division = $resource->types;
        $divisions = [$division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->name;
        }

        return $this->cache['divisions'][$resource->id] = array_reverse($divisions);
    }

    protected function getBoq($costShadow)
    {

        $boqCode = $costShadow->wbs . '#' . $costShadow->cost_account;
        if (isset($this->cache['boqs'][$boqCode])) {
            return $this->cache['boqs'][$boqCode];
        } else {
            $boq = Boq::costAccountOnWbs($costShadow->wbs, $costShadow->cost_account)->first();
            if ($boq) {
                return $this->cache['boqs'][$boqCode] = $boq;
            }
        }

        return $this->cache['boqs'][$boqCode] = null;
    }

    private function generateWasteIndex()
    {
        WasteIndex::where('period_id', $this->period->id)->delete();

        BreakDownResourceShadow::with(['actual_resources', 'important_actual_resources'])
            ->where('project_id', $this->project->id)
            ->where('resource_type_id', 3)
            ->when($this->project->is_activity_rollup, function ($q) {
                return $q->where('important', true);
            })->each(function (BreakDownResourceShadow $resource) {
                if ($resource->is_rollup) {
                    return true;
                }

                $resource->setCalculationPeriod($this->period);

                $to_date_qty = $resource->actual_resources()->sum('qty') + $resource->important_actual_resources->sum('qty');
                if (!$to_date_qty) {
                    return true;
                }

                $to_date_cost = $resource->actual_resources()->sum('cost') + $resource->important_actual_resources->sum('cost');
                $to_date_unit_price = 0;
                $to_date_unit_price = $to_date_cost / $to_date_qty;

                $allowable_qty = $to_date_qty < $resource->budget_unit? $to_date_qty : $resource->budget_unit;
                if ($resource->progress == 100 || strtolower($resource->status) == 'closed') {
                    $allowable_qty = $resource->budget_unit;
                }

                $qty_var = $allowable_qty - $to_date_qty;
                $waste_var = $qty_var * $to_date_unit_price;
                $allowable_cost = $allowable_qty * $to_date_unit_price;
                $waste_index = 0;
                if ($allowable_cost) {
                    $waste_index = $waste_var * 100 / $allowable_cost;
                }

                $attributes = [
                    'project_id' => $this->project->id,
                    'period_id' => $this->period->id,
                    'breakdown_resource_id' => $resource->breakdown_resource_id,
                    'resource_id' => $resource->resource_id,
                    'resource_type_id' => $resource->resource->resource_type_id ?? $resource->resource_type_id,
                    'to_date_qty' => $to_date_qty,
                    'to_date_unit_price' => $to_date_unit_price,
                    'allowable_qty' => $allowable_qty,
                    'qty_var' => $qty_var,
                    'waste_var' => $waste_var,
                    'waste_index' => $waste_index,
                ];

                WasteIndex::forceCreate($attributes);
            });
    }
}
