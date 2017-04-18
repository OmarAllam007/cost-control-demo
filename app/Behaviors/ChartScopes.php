<?php

namespace App\Behaviors;

use Illuminate\Database\Eloquent\Builder;

trait ChartScopes
{
    function scopeBudgetVsCompletionChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(budget_cost) as budget_cost, sum(completion_cost) as completion_cost')->wherePeriodId($period_id);
    }

    function scopeTodateVsAllowableChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(to_date_cost) as to_date_cost, sum(allowable_ev_cost) as allowable_cost')->wherePeriodId($period_id);
    }

    function scopeTodateCostTrendChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(to_date_cost) as value')
            ->join('periods as p', 'p.id', '=', 'master_shadows.period_id')->selectRaw('p.name as name')
            ->groupBy('p.name')->orderBy('p.id');
    }

    function scopeCompletionCostTrendChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(completion_cost) as value')
            ->join('periods as p', 'p.id', '=', 'master_shadows.period_id')->selectRaw('p.name as name')
            ->groupBy('p.name')->orderBy('p.id');
    }

    function scopeTodateVarTrendChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(allowable_var) as value')
            ->join('periods as p', 'p.id', '=', 'master_shadows.period_id')->selectRaw('p.name as name')
            ->groupBy('p.name')->orderBy('p.id');
    }

    function scopeCompletionVarTrendChart(Builder $query, $period_id)
    {
        $query->selectRaw('sum(cost_var) as value')
            ->join('periods as p', 'p.id', '=', 'master_shadows.period_id')->selectRaw('p.name as name')
            ->groupBy('p.name')->orderBy('p.id');
    }

    function scopeCpiTrendChart(Builder $query)
    {
        $query->join('periods as p', 'p.id', '=', 'master_shadows.period_id')
            ->selectRaw('p.name as name, sum(allowable_ev_cost) / sum(to_date_cost) as value')
            ->groupBy('p.name')->orderBy('p.id');
    }

    function scopeChartFilter(Builder $query)
    {
        return $query->groupBy('master_shadows.project_id');
    }

    function scopeActivityChartFilter(Builder $query, $items)
    {
        return $query->whereIn('activity_id', $items)->addSelect('activity')->groupBy('activity_id', 'activity')->orderBy('activity');
    }

    function scopeResourceChartFilter(Builder $query, $items)
    {
        return $query->whereIn('resource_id', $items)->selectRaw('resource_name as resource')->groupBy('resource_name')->orderBy('resource_name');
    }

    function scopeResourceTypeChartFilter(Builder $query, $items)
    {

        $query->join('resource_types as rt', 'resource_type_id', '=', 'rt.id')->where(function (Builder $q) use ($items) {
            $firstItem = array_shift($items);
            $q->where('rt.name', 'like', "%$firstItem%");
            foreach ($items as $item) {
                $q->orWhere('rt.name', 'like', "%$item%");
            }
        });
        return $query->selectRaw('trim(rt.name) as resource_type')->groupBy(\DB::raw('trim(rt.name)'))->orderByRaw('trim(rt.name)');
    }

    function scopeBoqChartFilter(Builder $query, $items)
    {
        return $query->whereIn('boq_id', $items)->addSelect('boq')->groupBy('boq')->orderBy('boq');
    }
}