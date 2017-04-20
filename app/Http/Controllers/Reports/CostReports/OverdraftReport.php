<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 27/12/16
 * Time: 11:19 ص
 */

namespace App\Http\Controllers\Reports\CostReports;

use App\MasterShadow;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;

class OverdraftReport
{

    /** @var Project */
    protected $project;

    /** @var Collection  */
    protected $wbs_levels;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    public function run()
    {
        $tree = $this->buildTree();

        $project = $this->project;
        $periods = $this->project->periods()->readyForReporting()->pluck('name', 'id');

        return view('reports.cost-control.over-draft.over_draft', compact('tree', 'project', 'periods'));
    }

    protected function buildTree()
    {
        $tree = [];

        /** @var Collection $rawData */
        $rawData = MasterShadow::overDraftReport($this->period)->get()->groupBy('wbs_id');;

        $wbs_levels = WbsLevel::with('parent.parent.parent')->whereIn('id', $rawData->keys())->get()->keyBy('id')->map(function($level) {
            return $level->path_array;
        });

        foreach ($wbs_levels as $wbs_id => $levelPath) {
            $parent = '';
            foreach ($levelPath as $level) {
                if (!isset($tree[$level])) {
                    $tree[$level] = ['name' => $level, 'parent' => $parent, 'boqs' => []];
                }

                $parent = $level;
            }

            if ($rawData->has($wbs_id)) {
                $tree[$parent]['boqs'] = $rawData->get($wbs_id);
            }
        }

        return collect($tree);
    }
}