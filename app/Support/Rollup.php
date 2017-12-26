<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 26/12/17
 * Time: 3:21 PM
 */

namespace App\Support;


use App\ActualResources;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\WbsLevel;
use Illuminate\Support\Collection;

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

    protected $rollUpResource;

    function __construct($key, $input)
    {
        $data = \Cache::get($key);
        $this->project = $data['project'];
        $this->wbsLevel = $data['wbsLevel'];
        $this->stdActivity = $data['stdActivity'];
        $this->resources = $data['resources'];
        $this->input = $input;
    }

    function handle()
    {
        $code = $this->resources->first()->code;

        if ($this->input['progress'] == 0) {
            $status = 'Not Started';
        } elseif ($this->input['progress'] == 100) {
            $status = 'In Progress';
        } else {
            $status = 'Closed';
        }

        BreakDownResourceShadow::unguard();
        $this->rollUpResource = BreakDownResourceShadow::create([
            'project_id' => $this->id, 'wbs_id' => $this->id, 'activity_id' => $this->id, 'activity' => $this->name,
            'resource_code' => $this->input['code'], 'resource_name' => $this->input['name'], 'resource_type_id' => $this->input['type'],
            'budget_unit' => $this->input('qty'), 'budget_cost' => $this->sum('budget_cost'),
            'measure_unit' => 'LM', 'unit_id' => 3,
            'code' => $code, 'progress' => $this['progress'], 'status' => $status,
            'show_in_budget' => false, 'show_in_cost' => true, 'is_rolled_up' => true
        ]);

        $this->updateResources();
        $this->updateCost();
    }

    private function updateResources()
    {
        $this->resources->each(function($resource) {
            $resource->show_in_budget = true;
            $resource->show_in_cost = false;
            $resource->rollup_resource_id = $this->rollUpResource->id;

            $resource->save();
        });
    }

    private function updateCost()
    {
        $period = $this->project->open_period();

        $actual_resource = new ActualResources([
            'project_id' => $this->project->id,
            'wbs_level_id' => $this->wbsLevel->id,
            'period_id' => $period->id,
            'breakdown_resource_is'
        ]);

    }


}