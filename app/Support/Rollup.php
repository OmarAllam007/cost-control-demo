<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 26/12/17
 * Time: 3:21 PM
 */

namespace App\Support;


use App\ActualResources;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\StdActivity;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class Rollup
{
    /** @var Project */
    private $project;

    /** @var WbsLevel */
    private $wbsLevel;

    /** @var StdActivity */
    private $stdActivity;

    /** @var Collection */
    private $resources;

    /** @var array */
    private $input;

    /** @var BreakDownResourceShadow */
    private $rollUpShadow;

    private $rollupBreakdown;
    private $rollupBreakdownResource;

    function __construct($key, $input)
    {
        $data = \Cache::get($key);
        $this->project = $data['project'];
        $this->wbsLevel = $data['wbsLevel'];
        $this->stdActivity = $data['stdActivity'];
        $this->resources = $data['resources'];
        $this->input = new Fluent($input);
    }

    function handle()
    {
        $this->breakdown();
        $this->breakdownResource();
        $this->shadow();
        $this->updateResources();
        $this->updateCost();
    }

    private function breakdown()
    {
        Breakdown::unguard();
        Breakdown::flushEventListeners();
        return $this->rollupBreakdown = Breakdown::create([
            'project_id' => $this->project->id,
            'wbs_level_id' => $this->wbsLevel->id,
            'std_activity_id' => $this->stdActivity->id,
            'template_id' => 0,
            'cost_account' => '',
            'code' => $this->input['code']
        ]);
    }

    private function breakdownResource()
    {
        BreakdownResource::unguard();
        BreakdownResource::flushEventListeners();
        return $this->rollupBreakdownResource = BreakdownResource::create([
            'breakdown_id' => $this->rollupBreakdown->id,
            'std_activity_resource_id' => 0,
            'budget_qty' => $this->input['qty'],
            'eng_qty' => $this->input['qty'],
            'resource_qty' => $this->input['qty'],
            'resource_qty_manual' => $this->input['qty'],
            'code' => $this->input['code'],
            'resource_id' => 0,
            'equation' => '$v'
        ]);
    }

    private function shadow()
    {
        $code = $this->resources->first()->code;

        $status = $this->status();

        BreakDownResourceShadow::unguard();
        BreakDownResourceShadow::flushEventListeners();
        $this->rollUpShadow = BreakDownResourceShadow::create([
            'project_id' => $this->project->id, 'wbs_id' => $this->wbsLevel->id,
            'activity_id' => $this->stdActivity->id, 'activity' => $this->name,
            'breakdown_id' => $this->rollupBreakdown->id, 'breakdown_resource_id' => $this->rollupBreakdownResource->id,
            'resource_id' => 0, 'template' => 'Rollup', 'template_id' => 0, 'resource_waste' => 0, 'cost_account' => '',
            'resource_code' => $this->input['code'], 'resource_name' => $this->input['name'], 'resource_type_id' => $this->input['type'],
            'budget_unit' => $this->input['qty'], 'budget_cost' => $this->resources->sum('budget_cost'),
            'resource_qty' => $this->input['qty'],
            'measure_unit' => 'LM', 'unit_id' => 3, 'unit_price' => 0, 'remarks' => 'Rollup',
            'code' => $code, 'progress' => $this['progress'], 'status' => $status,
            'show_in_budget' => false, 'show_in_cost' => true, 'is_rolled_up' => true
        ]);
    }

    private function updateResources()
    {
        $this->resources->each(function ($resource) {
            $resource->show_in_budget = true;
            $resource->show_in_cost = false;
            $resource->rollup_resource_id = $this->rollUpShadow->id;

            $resource->save();
        });
    }

    private function updateCost()
    {
        $period = $this->project->open_period();

        $breakdown_resource_ids = $this->resources->pluck('breakdown_resource_id');
        $old = ActualResources::whereIn('breakdown_resource_id', $breakdown_resource_ids)->get();

        $original_data = $old->pluck('original_data');

        $to_date_cost = $old->sum('to_date_cost');
        $to_date_qty = $old->sum('to_date_qty');
        $to_date_unit_price = $to_date_cost / $to_date_qty;

        ActualResources::unguard();
        ActualResources::flushEventListeners();
        ActualResources::create([
            'project_id' => $this->project->id,
            'wbs_level_id' => $this->wbsLevel->id,
            'period_id' => $period->id,
            'breakdown_resource_id' => $this->rollupBreakdownResource->id,
            'unit_price' => $to_date_unit_price,
            'cost' => $to_date_cost,
            'qty' => $to_date_qty,
            'progress' => $this->input['progress'],
            'action_date' => date('Y-m-d'),
            'user_id' => \Auth::id(),
            'original_data' => $original_data,
        ]);

        $shadow = $this->rollUpShadow->fresh();
        $shadow->appendFields();
        CostShadow::create($shadow->toArray());

        $old->each(function ($resource) {
            $resource->delete();
        });

        CostShadow::whereIn('breakdown_resource_id', $breakdown_resource_ids)
            ->get()->each(function ($resource) {
                $resource->delete();
            });
    }

    private function status()
    {
        if ($this->input['progress'] == 0) {
            return $status = 'Not Started';
        } elseif ($this->input['progress'] == 100) {
            return $status = 'In Progress';
        }

        return $status = 'Closed';
    }
}