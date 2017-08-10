<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/9/17
 * Time: 3:30 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;

class QsSummaryReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $info;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $activities;

    /** @var Collection */
    protected $survies;

    /** @var Collection */
    protected $tree;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function run()
    {
        /** @var Collection $shadow_data */
        $shadow_data = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT wbs_id, activity_id, cost_account, budget_qty, eng_qty, boq_id, survey_id')
            ->get();

        $this->info = $shadow_data->groupBy('wbs_id')->map(function (Collection $group) {
            return $group->groupBy('activity_id');
        });

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = StdActivity::with('division')->find($shadow_data->pluck('activity_id')->toArray())->keyBy('id');
        $this->survies = Survey::with('unit')->find($shadow_data->pluck('survey_id')->toArray())->keyBy('id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    protected function buildTree($parent = 0)
    {
        $tree = $this->wbs_levels->get($parent) ?: collect();

        $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $level->activities = collect();
            if ($this->info->has($level->id)) {
                $info = $this->info->get($level->id);
                $activity_ids = $info->keys();
                $level->activities = $this->activities->only($activity_ids->toArray())
                    ->map(function ($activity) use ($info) {
                        $activity->cost_accounts = $info->get($activity->id)->map(function($cost_account){
                            $cost_account->boq_description = $this->survies->get($cost_account->survey_id)->description ?? '';
                            $cost_account->unit_of_measure = $this->survies->get($cost_account->survey_id)->unit->type ?? '';
                            return $cost_account;
                        });
                        return $activity;
                    })->groupBy('division.name')->sortByKeys();
            }

            return $level;
        });

        return $tree;
    }

    function excel()
    {

    }
}