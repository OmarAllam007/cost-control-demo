<?php

namespace App\Behaviors;


use App\Period;
use App\StdActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

trait ReportScopes
{
    public function scopeResourceDictReport(Builder $query)
    {
        $fields = ['master_shadows.resource_id', 'resources.resource_type_id', 'resource_name', 'master_shadows.top_material'];
        $query->select($fields);
        $query->selectRaw(
            'sum(prev_cost) prev_cost, sum(prev_qty) prev_qty,' .
            'sum(curr_cost) curr_cost, sum(curr_qty) curr_qty,' .
            'sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_var) as to_date_var, sum(allowable_ev_cost) to_date_allowable, sum(allowable_qty) to_date_allowable_qty,' .
            'sum(remaining_cost) as remaining_cost, sum(remaining_qty) as remaining_qty, CASE WHEN sum(allowable_qty) != 0 THEN (sum(allowable_qty) - sum(to_date_qty)) / sum(allowable_qty) ELSE 0 END AS pw_index,' .
            'sum(completion_cost) at_completion_cost, sum(completion_qty) at_completion_qty, sum(cost_var) cost_var, sum(budget_cost) budget_cost, sum(budget_unit) budget_qty, sum(qty_var) as qty_var'
        );

        $query->join('resources', 'master_shadows.resource_id', '=', 'resources.id');

        $query->groupBy($fields)->orderByRaw('3');
        return $query;
    }

    public function scopeVarAnalysisReport(Builder $query)
    {
        $query->selectRaw(
            'resource_code, resource_name, resource_type_id, act.discipline as boq_discipline, trim(rt.name) as resource_type, avg(unit_price) as budget_unit_price,'.
            '(CASE WHEN sum(prev_qty) = 0 THEN 0 ELSE sum(prev_cost) / sum(prev_qty) END) as prev_unit_price, ' .
            '(CASE WHEN sum(curr_qty) = 0 THEN 0 ELSE sum(curr_cost) / sum(curr_qty) END) AS curr_unit_price,' .
            '(CASE WHEN sum(to_date_qty) = 0 THEN 0 ELSE sum(to_date_cost) / sum(to_date_qty) END) AS to_date_unit_price,' .
            'sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_qty) to_date_allowable_qty,' .
            'sum(cost_variance_completion_due_unit_price) cost_unit_price_var, sum(cost_variance_completion_due_qty) cost_qty_var'
        );

        $query->join('resource_types as rt', 'resource_type_id', '=', 'rt.id');
        $query->join('std_activities as act', 'activity_id', '=', 'act.id');

        $query->groupBy(['resource_code', 'resource_name', 'resource_type_id', 'boq_discipline', 'resource_type'])->orderByRaw('4, 5, 2');
        return $query;
    }

    function scopePreviousActivityReport(Builder $query, Period $period)
    {
        $query->forPeriod($period);
        $fields = ['wbs_id', 'activity', 'resource_name', 'activity_id'];
        $query->groupBy($fields);
        $query->select($fields)->selectRaw(
            'sum(to_date_cost) prev_cost, sum(allowable_ev_cost) prev_allowable, sum(allowable_var) prev_cost_var'
        );
        $query->orderBy('resource_name', 'activity');
    }

    function scopeCurrentActivityReport(Builder $query, Period $period)
    {
        $query->forPeriod($period);
        $fields = ['wbs_id', 'activity', 'activity_id', 'resource_name'];
        $query->groupBy($fields);
        $query->select($fields)->selectRaw(
            'sum(budget_cost) as budget_cost, sum(to_date_cost) to_date_cost, sum(to_date_qty) to_date_qty, sum(allowable_ev_cost) to_date_allowable,'.
            'sum(allowable_var) as to_date_var, sum(remaining_cost) remaining_cost, sum(completion_cost) completion_cost, sum(cost_var) completion_var'
        );
        $query->orderBy('resource_name', 'activity');
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

    function scopeOverDraftReport(Builder $query, Period $period)
    {
        $query->from('master_shadows as sh')
            ->leftJoin('actual_revenue as rev', function (JoinClause $join) {
                $join->on('sh.boq_id', '=', 'rev.boq_id')
                    ->on('sh.period_id', '=', 'rev.period_id');
            })->join('boqs', 'sh.boq_id', '=', 'boqs.id')
            ->where('sh.period_id', $period->id)
            ->select('sh.boq_id', 'boqs.description')
            ->selectRaw('boq_wbs_id as wbs_id, avg(boqs.quantity) as boq_quantity, avg(boqs.price_ur) as boq_unit_price')
            ->selectRaw('sum(sh.physical_unit) as physical_unit, sum(to_date_qty * sh.unit_price/budget_unit_rate) as physical_unit_upv')
            ->selectRaw('sum(sh.physical_unit * boqs.price_ur) as physical_revenue, sum((sh.to_date_qty * sh.unit_price/sh.budget_unit_rate) * boqs.price_ur) as physical_revenue_upv')
            ->selectRaw('avg(rev.value) as actual_revenue')
            ->groupBy('boq_wbs_id', 'sh.boq_id', 'boqs.description')
        ->where(function(Builder $q) {
            $q->where('sh.to_date_qty', '>', 0)->orWhere(function ($q) {
                $q->whereNotNull('rev.value')->where('rev.value', '>', 0);
            });
        })->where('boqs.price_ur', '!=', 0)
        ->orderBy('boqs.description');

        return $query;
    }

    function scopeDashboardSummary($query, $period)
    {
        $general_activities = StdActivity::where('division_id', 779)->pluck('id')->implode(', ');

        return $query->where('period_id', $period->id)
            ->selectRaw("(CASE WHEN activity_id IN ($general_activities) THEN 'INDIRECT' WHEN activity_id = 3060 THEN 'MANAGEMENT RESERVE' ELSE 'DIRECT' END) AS 'type'")
            ->selectRaw('SUM(prev_cost) AS previous_cost')
            ->selectRaw('SUM(allowable_ev_cost) AS allowable_cost, SUM(budget_cost) AS budget_cost')
            ->selectRaw('SUM(to_date_cost) AS to_date_cost, SUM(remaining_cost) AS remaining_cost')
            ->selectRaw('SUM(completion_cost) AS completion_cost, SUM(allowable_var) as to_date_var')
            ->selectRaw('SUM(completion_cost_optimistic) AS completion_cost_optimistic, SUM(completion_var_optimistic) AS completion_var_optimistic')
            ->selectRaw('SUM(completion_cost_likely) AS completion_cost_likely, SUM(completion_var_likely) AS completion_var_likely')
            ->selectRaw('SUM(completion_cost_pessimistic) AS completion_cost_pessimistic, SUM(completion_var_pessimistic) AS completion_var_pessimistic')
            ->selectRaw('SUM(cost_var) AS completion_var')
            ->groupBy('type');
    }
}