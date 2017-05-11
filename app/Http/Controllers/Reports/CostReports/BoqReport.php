<?php
namespace App\Http\Controllers\Reports\CostReports;

use App\Boq;
use App\MasterShadow;
use App\Project;
use App\Period;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BoqReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Collection */
    protected $wbs_levels;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->wbs_levels = new Collection();
    }

    function run()
    {
        $project = $this->project;

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->orderBy('name')->pluck('name', 'id');

        return view('reports.cost-control.boq-report.boq_report', compact('tree', 'project', 'periods'));
    }

    function buildTree()
    {
        $tree = [];

        $query = MasterShadow::with('boq_record', 'boq_wbs')->boqReport($this->period);
        $currentData = $this->applyFilters($query)->get();
        foreach ($currentData as $boq) {

            if ($this->wbs_levels->has($boq->boq_wbs_id)) {
                $levels = $this->wbs_levels->get($boq->boq_wbs_id);
            } else {
                if (!$boq->boq_wbs) {
                    continue;
                }
                $levels = $boq->boq_wbs->getParents();
                $this->wbs_levels->put($boq->boq_wbs_id, $levels);
            }

            $key = '';
            $lastKey = '';
            foreach ($levels as $level) {
                $lastKey = $key;
                $key .= $level;

                if (!isset($tree[$key])) {
                    $tree[$key] = [
                        'boqs' => [], 'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                        'remaining_cost' => 0, 'at_completion_cost' => 0, 'at_completion_var' => 0, 'dry_cost' => 0, 'boq_cost' => 0
                    ];
                }

                $tree[$key]['name'] = $level;
                $tree[$key]['key'] = $key;
                $tree[$key]['parent'] = $lastKey;
                $tree[$key]['dry_cost'] += $boq->boq_record->dry_ur * $boq->boq_record->quantity;
                $tree[$key]['boq_cost'] += $boq->boq_record->price_ur * $boq->boq_record->quantity;
                $tree[$key]['budget_cost'] += $boq->budget_cost;
                $tree[$key]['to_date_cost'] += $boq->to_date_cost;
                $tree[$key]['to_date_allowable'] += $boq->to_date_allowable;
                $tree[$key]['to_date_var'] += $boq->to_date_var;
                $tree[$key]['remaining_cost'] += $boq->remaining_cost;
                $tree[$key]['at_completion_cost'] += $boq->at_completion_cost;
                $tree[$key]['at_completion_var'] += $boq->at_completion_var;

                $lastKey = $key;
            }

            $tree[$lastKey]['boqs'][] = [
                'cost_account' => $boq->cost_account,
                'description' => $boq->boq_record->description,
                'dry_price' => $boq->boq_record->dry_ur,
                'boq_price' => $boq->boq_record->price_ur,
                'budget_unit_rate' => $boq->budget_unit_rate,
                'dry_cost' => $boq->boq_record->dry_ur * $boq->boq_record->quantity,
                'boq_cost' => $boq->boq_record->price_ur * $boq->boq_record->quantity,
                'physical_qty' => $boq->physical_qty,
                'boq_qty' => $boq->boq_record->quantity,
                'budget_qty' => $boq->budget_qty,
                'budget_cost' => $boq->budget_cost,
                'to_date_cost' => $boq->to_date_cost,
                'to_date_allowable' => $boq->to_date_allowable,
                'to_date_var' => $boq->to_date_var,
                'remaining_cost' => $boq->remaining_cost,
                'at_completion_cost' => $boq->at_completion_cost,
                'at_completion_var' => $boq->at_completion_var,
            ];
        }

        return collect($tree);
    }

    function applyFilters(Builder $query)
    {
        $request = request();

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($wbs = $request->get('wbs')) {
            $term = "%$wbs%";
            $levels = WbsLevel::where('project_id', $this->project->id)->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)->orWhere('name', 'like', $term);
            })->pluck('id');
            $query->whereIn('wbs_id', $levels);
        }

        if ($cost_account = $request->get('cost_account')) {
            $query->where('cost_account', 'like', "%$cost_account%");
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_var < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('at_completion_var', '<', 0);
        }

        return $query;
    }

}