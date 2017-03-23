<?php

namespace App\Jobs\Export;

use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Job;
use App\Resources;
use App\ResourceType;
use App\StdActivity;
use App\WbsLevel;
use App\WbsResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ExportCostShadow extends Job
{

    protected $project;
    /**
     * @var string
     */
    private $perspective;

    /** @var Collection */
    protected $lines;

    protected $buffer = '';

    protected $cache = ['activity' => [], 'wbs' => []];

    public function __construct($project, $perspective = '')
    {
        $this->project = $project;
        $this->perspective = $perspective;
    }
    protected $resources ;
    protected $activities ;

    public function handle()
    {

        $this->resources = Resources::all()->keyBy('id')->map(function ($resource) {
        return $resource;
        });

        $this->activities = StdActivity::all()->keyBy('id')->map(function ($activity) {
        return $activity;
        });

        set_time_limit(3600);
        $headers = [
            'WBS Level 1', 'WBS Level 2', 'WBS Level 3', 'WBS Level 4', 'WBS Level 5', 'WBS Level 6',
            'Activity Division 1', 'Activity Division 2', 'Activity Division 3', 'Activity Name', 'Activity ID',
            'BOQ Description', 'Cost Account', 'Eng Quantity', 'Budget Quantity', 'Resource Quantity', 'Resource Waste',
            'Resource Type', 'Resource Division', 'Resource Sub Division', 'Resource Code', 'Resource Name', 'Top Material',
            'Price/Unit', 'Unit Of Measure', 'Budget Unit', 'Budget Cost', 'BOQ Equivalent Unit rate', 'Budget Unit Rate',
            'No. Of Labors', 'Productivity (Unit/Day)', 'Productivity Ref', 'Remarks',
            'Progress', 'Status',
            'Prev. Price/Unit', 'Prev. Quantity', 'Prev. Cost', 'Current. Price/Unit', 'Current Quantity', 'Current Cost',
            'To Date Price/Unit(Eqv)', 'To Date Quantity', 'To Date Cost', 'Allowable (EV) cost', 'Var +/-',
            'Remaining Price/Unit', 'Remaining Qty', 'Remaining Cost', 'BL Allowable Cost', 'Var +/- 10',
            'Completion Price/Unit', 'Completion Qty', 'Completion Cost', 'Price/Unit Var', 'Qty Var +/-', 'Cost Var +/-',
            'Physical Unit', '(P/W) Index', 'Cost Variance To Date Due to Unit Price', 'Allowable Quantity', 'Cost Variance Remaining Due to Unit Price',
            'Cost Variance Completion Due to Unit Price', 'Cost Variance Completion Due to Qty', 'Cost Variance to Date Due to Qty',
        ];

        $this->buffer = implode(',', array_map('csv_quote', $headers));

        $period = $this->project->open_period();


        if ($this->perspective == 'budget') {
            $query = BreakDownResourceShadow::joinCost(null, $period)->with('wbs')->where('budget.project_id', $this->project->id);
        } else {
            $query = CostShadow::joinShadow(null, $period);
        }
        /** @var $query Builder */
        $query->chunk(10000, function ($shadows) {
            $time = microtime(1);
            foreach ($shadows as $costShadow) {
                $boqDescription = $this->getBoqDescription($costShadow);

                if (isset($this->cache['resources'][$costShadow->resource_id])) {
                    $resource = $this->cache['resources'][$costShadow->resource_id];
                } else {
                    $this->cache['resources'][$costShadow->resource_id] = $resource = $this->resources->get($costShadow->resource_id);
                }
                $wbs = $this->getWbs($costShadow);
                $activityDivs = $this->getActivityDivisions($costShadow);

                $this->buffer .= "\n" .
                    $wbs.','.
                    $activityDivs.','.
                    csv_quote($costShadow['activity']).','.
                    '"'.$costShadow['code'].'",'.
                    csv_quote($boqDescription).','.
                    '"'.$costShadow['cost_account'].'",'.
                    round($costShadow['eng_qty'] ?: '0', 2).','.
                    round($costShadow['budget_qty'] ?: '0', 2).','.
                    round($costShadow['resource_qty'] ?: '0', 2).','.
                    round($costShadow['resource_waste'] ?: '0', 2).','.
                    $this->getResourceDivisions($resource).','.
                    '"'.$costShadow['resource_code'].'",'.
                    csv_quote($costShadow['resource_name']).','.
                    csv_quote($resource->top_material) . ','.
                    '"'.round($costShadow['unit_price'] ?: '0', 2).'",'.
                    '"'.$costShadow['measure_unit'].'",'.
                    '"'.round($costShadow['budget_unit'] ?: '0', 2).'",'.
                    '"'.round($costShadow['budget_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['boq_equivilant_rate'] ?: '0', 2).'",'.
                    '"'.round($costShadow['budget_unit_rate'] ?: '0', 2).'",'.
                    '"'.round($costShadow['labors_count'], 2).'",'.
                    '"'.round($costShadow['productivity_output'] ?: '0', 2).'",'.
                    '"'.$costShadow['productivity_ref'].'",'.
                    '"'.$costShadow['remarks'].'",'.
                    '"'.round($costShadow['progress'], 2).'",'.
                    '"'.($costShadow['status'] ?: 'Not Started').'",'.
                    '"'.round($costShadow['prev_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['prev_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['prev_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['curr_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['curr_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['curr_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['to_date_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['to_date_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['to_date_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['allowable_ev_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['allowable_var'] ?: '0', 2).'",'.
                    '"'.round($costShadow['remaining_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['remaining_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['remaining_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['bl_allowable_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['bl_allowable_var'] ?: '0', 2).'",'.
                    '"'.round($costShadow['completion_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['completion_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['completion_cost'] ?: '0', 2).'",'.
                    '"'.round($costShadow['unit_price_var'] ?: '0', 2).'",'.
                    '"'.round($costShadow['qty_var'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_var'] ?: '0', 2).'",'.
                    '"'.round($costShadow['physical_unit'] ?: '0', 2).'",'.
                    '"'.round($costShadow['pw_index'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_variance_to_date_due_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['allowable_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_variance_remaining_due_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_variance_completion_due_unit_price'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_variance_completion_due_qty'] ?: '0', 2).'",'.
                    '"'.round($costShadow['cost_variance_to_date_due_qty'] ?: '0',2) . '"';
            }

            unset($shadows);
            gc_collect_cycles();

            \Log::info('Chunk has been buffered; memory: ' . round(memory_get_usage() / (1024 * 1024), 2));
        });

        return $this->buffer;
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
        $levelsCount = count($wbsLevels);

        if ($levelsCount < 6) {
            for ($i = $levelsCount; $i < 6; ++$i) {
                $wbsLevels[$i] = '';
            }
        } elseif($levelsCount > 6) {
            for ($i = 6; $i < $levelsCount; ++$i) {
                unset($wbsLevels[$i]);
            }
            $wbsLevels[5] = $level->name;
        }

        return $this->cache['wbs'][$level->id] = implode(',', array_map('csv_quote', $wbsLevels));
    }

    protected function getActivityDivisions($costShadow)
    {
        if (isset($this->cache['activity'][$costShadow->activity_id])) {
            return $this->cache['activity'][$costShadow->activity_id];
        }

        $activity = $this->activities->get($costShadow->activity_id);
        $division = $parent =$activity->division;
        $divisions = [$division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->name;
        }
        $divisions = array_reverse($divisions);
        $divisionsCount = count($divisions);
        if ($divisionsCount < 3) {
            for ($i = $divisionsCount; $i < 3; ++$i) {
                $divisions[$i] = '';
            }
        } elseif($divisionsCount > 3) {
            for ($i = 3; $i < $divisionsCount; ++$i) {
                unset($divisions[$i]);
            }
            $divisions[2] = $division->name;
        }

        return $this->cache['activity'][$costShadow->activity_id] = implode(',', array_map('csv_quote', $divisions));
    }

    protected function getResourceDivisions($resource)
    {
        if (isset($this->cache['divisions'][$resource->id])) {
            return $this->cache['divisions'][$resource->id];
        }

        $parent = $division = $resource->types;
        $divisions = [$division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->name;
        }
        $divisions = array_reverse($divisions);
        $divisionsCount = count($divisions);
        if ($divisionsCount < 3) {
            for ($i = $divisionsCount; $i < 3; ++$i) {
                $divisions[$i] = '';
            }
        } elseif($divisionsCount > 3) {
            for ($i = 3; $i < $divisionsCount; ++$i) {
                unset($divisions[$i]);
            }
            $divisions[2] = $division->name;
        }
        return $this->cache['divisions'][$resource->id] = implode(',', array_map('csv_quote', $divisions));
    }

    protected function getBoqDescription($costShadow)
    {
        $boqDescription = '';
        $boqCode = $costShadow->wbs . '#'. $costShadow->cost_account;
        if (isset($this->cache['boqs'][$boqCode])) {
            return $this->cache['boqs'][$boqCode];
        } else {
            $boq = Boq::costAccountOnWbs($costShadow->wbs, $costShadow->cost_account)->first();
            if ($boq) {
                return $this->cache['boqs'][$boqCode] = $boq->description;
            }
        }

        return $this->cache['boqs'][$boqCode] = '';
    }
}
