<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MasterShadow extends Model
{
    protected $casts = [
        'wbs' => 'array',
        'activity_divs' => 'array',
        'resource_divs' => 'array',
    ];

    function wbs_level()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    function boq_record()
    {
        return $this->belongsTo(Boq::class, 'boq_id');
    }

    function boq_wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'boq_wbs_id');
    }

    public function scopeForPeriod(Builder $query, Period $period)
    {
        return $query->wherePeriodId($period->id)->whereProjectId($period->project_id);
    }

    public function scopeResourceDictReport(Builder $query)
    {
        $fields = ['resource_name', 'resource_type_id', 'boq_discipline', 'top_material'];
        $query->select($fields);
        $query->selectRaw(
            'trim(rt.name) as resource_type, sum(prev_cost) prev_cost, sum(prev_qty) prev_qty,' .
            'sum(curr_cost) curr_cost, sum(curr_qty) curr_qty,' .
            'sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_ev_cost) to_date_allowable, sum(allowable_qty) to_date_allowable_qty,' .
            'sum(remaining_cost) as remaining_cost, sum(remaining_qty) as remaining_qty, CASE WHEN sum(allowable_qty) != 0 THEN (sum(allowable_qty) - sum(to_date_qty)) / sum(allowable_qty) ELSE 0 END AS pw_index,' .
            'sum(completion_cost) at_completion_cost, sum(completion_qty) at_completion_qty, sum(cost_var) cost_var, sum(budget_cost) budget_cost, sum(budget_unit) budget_qty, sum(qty_var) as qty_var'
        );

        $query->join('resource_types as rt', 'resource_type_id', '=', 'rt.id');

        $fields[] = 'resource_type';
        $query->groupBy($fields)->orderByRaw('5, 3, 4, 1');
        return $query;
    }

    public function scopeVarAnalysisReport(Builder $query)
    {
        $fields = ['resource_name', 'resource_type_id', 'boq_discipline'];
        $query->select($fields);
        $query->selectRaw(
            'trim(rt.name) as resource_type, avg(unit_price) as budget_unit_price,'.
            '(CASE WHEN sum(prev_qty) = 0 THEN 0 ELSE sum(prev_cost) / sum(prev_qty) END) as prev_unit_price, ' .
            '(CASE WHEN sum(curr_qty) = 0 THEN 0 ELSE sum(curr_cost) / sum(curr_qty) END) AS curr_unit_price,' .
            '(CASE WHEN sum(to_date_qty) = 0 THEN 0 ELSE sum(to_date_cost) / sum(to_date_qty) END) AS to_date_unit_price,' .
            'sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_qty) to_date_allowable_qty,' .
            'sum(cost_variance_completion_due_unit_price) cost_unit_price_var, sum(cost_variance_completion_due_qty) cost_qty_var'
        );

        $query->join('resource_types as rt', 'resource_type_id', '=', 'rt.id');

        $fields[] = 'resource_type';
        $query->groupBy($fields)->orderByRaw('4, 3, 1');
        return $query;
    }

    function scopePreviousActivityReport(Builder $query, Period $period)
    {
        $query->forPeriod($period);
        $fields = ['wbs_id', 'activity'];
        $query->groupBy($fields);
        $query->select($fields)->selectRaw(
            'sum(to_date_cost) prev_cost, sum(allowable_ev_cost) prev_allowable, sum(allowable_var) prev_cost_var'
        );
        $query->orderBy('activity');
    }

    function scopeCurrentActivityReport(Builder $query, Period $period)
    {
        $query->forPeriod($period);
        $fields = ['wbs_id', 'activity'];
        $query->groupBy($fields);
        $query->select($fields)->selectRaw(
            'sum(budget_cost) as budget_cost, sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_ev_cost) to_date_allowable,'.
            'sum(allowable_var) as to_date_var, sum(remaining_cost) remaining_cost, sum(completion_cost) completion_cost, sum(cost_var) completion_var'
        );
        $query->orderBy('activity');
    }

    function scopeBoqReport(Builder $query, Period $period)
    {
        $fields = ['boq_wbs_id', 'cost_account', 'boq', 'boq_id'];

        $query->forPeriod($period)->select($fields)->selectRaw(
            'sum(budget_cost) as budget_cost, sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable,'.
            'sum(allowable_var) to_date_var, sum(remaining_cost) remaining_cost, sum(completion_cost) at_completion_cost,'.
            'sum(cost_var) at_completion_var, sum(physical_unit) physical_qty, avg(budget_unit_rate) as budget_unit_rate, avg(budget_qty) as budget_qty'
        )->groupBy($fields)->orderBy('cost_account');

        return $query;
    }
}
