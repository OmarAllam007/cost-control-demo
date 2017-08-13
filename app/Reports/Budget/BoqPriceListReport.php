<?php

namespace App\Reports\Budget;


use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;

class BoqPriceListReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $cost_accounts;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $raw_cost_accounts = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('wbs_id, survey_id, resource_type, sum(budget_cost) as budget_cost')
            ->groupBy(['wbs_id', 'survey_id', 'resource_type'])->get();
        $time = microtime(1);

        $this->surveys = Survey::with('unit')
            ->find($raw_cost_accounts->pluck('survey_id')->unique()->toArray())
            ->keyBy('id');

        $this->cost_accounts = $raw_cost_accounts->groupBy('wbs_id')->map(function (Collection $surveys) {
            return $surveys->groupBy('survey_id')->map(function (Collection $resource_types, $survey_id) {
                $survey = $this->surveys->get($survey_id);
                return collect([
                    'description' => $survey->description ?? '',
                    'unit_of_measure' => $survey->unit->type ?? '',
                    'budget_qty' => $survey->budget_qty ?? 0,
                    'cost_account' => $survey->cost_account ?? '',
                    'types' => $resource_types->map(function ($type) {
                        $type->resource_type = strtolower($type->resource_type);
                        return $type;
                    })->pluck('budget_cost', 'resource_type'),
                    'grand_total' => $resource_types->sum('budget_cost'),
                ]);
            });
        });

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent_id
     * @return Collection
     */
    protected function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $level->cost_accounts = $this->cost_accounts->get($level->id) ?: collect();

            $level->cost = $level->cost_accounts->sum('grand_total') + $level->subtree->sum('cost');

            return $level;
        });

        return $tree;
    }

    function excel()
    {

    }

    protected function buildExcel()
    {

    }
}