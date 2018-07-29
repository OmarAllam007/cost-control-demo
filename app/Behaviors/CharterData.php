<?php

namespace App\Behaviors;


use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;

trait CharterData
{
//    protected $cached_eac_contract_amount = null;

    function getTenderTotalCostAttribute()
    {
        return $this->tender_direct_cost + $this->tender_indirect_cost +
            $this->tender_risk + $this->tender_initial_profit;
    }

    function getTenderInitialProfitabilityIndexAttribute()
    {
        if (!$this->project_contract_signed_value) {
            return 0;
        }

        return $this->tender_initial_profit * 100 / $this->project_contract_signed_value;
    }

    function getDirectBudgetCostAttribute()
    {
        return $this->shadows()->budgetOnly()->whereNotIn('resource_type_id', [1, 8])->sum('budget_cost');
    }

    function getGeneralRequirementCostAttribute()
    {
        return $this->shadows()->where('resource_type_id', 1)->sum('budget_cost');
    }

    function getManagementReserveCostAttribute()
    {
        return $this->shadows()->budgetOnly()->where('resource_type_id', 8)->sum('budget_cost');
    }

    function getBudgetCostAttribute()
    {
        if (!is_null($this->cached_budget_cost)) {
            return $this->cached_budget_cost;
        }

        return $this->cached_budget_cost = $this->shadows()->budgetOnly()->sum('budget_cost');
    }

    function getEacContractAmountAttribute()
    {
        if (!is_null($this->cached_eac_contract_amount)) {
            return $this->cached_eac_contract_amount;
        }

        $this->cached_eac_contract_amount = \DB::table('boqs')
            ->where('boqs.project_id', $this->id)
            ->join('qty_surveys as qs', function (JoinClause $on) {
//                $on->on('boqs.id', '=', 'qs.boq_id');
                $on->on('boqs.wbs_id', '=', 'qs.wbs_level_id');
                $on->on('boqs.cost_account', '=', 'qs.cost_account');
            })->selectRaw('sum(boqs.price_ur * qs.eng_qty) as revised_boq')
            ->value('revised_boq');

        return $this->cached_eac_contract_amount;
    }

    function getPlannedProfitAmountAttribute()
    {
        return $this->eac_contract_amount - $this->budget_cost;
    }

    function getPlannedProfitabilityAttribute()
    {
        if (!$this->eac_contract_amount) {
            return 0;
        }

        return $this->planned_profit_amount * 100 / $this->eac_contract_amount;
    }

    function getProjectDurationAttribute()
    {
        if ($this->attributes['project_duration']) {
            return $this->attributes['project_duration'];
        }

        if ($this->expected_finish_date && $this->project_start_date) {
            $start = Carbon::parse($this->project_start_date);
            $finish = Carbon::parse($this->expected_finish_date);

            return $finish->diffInDays($start);
        }

        return '';
    }

    function getSwCostPerM2Attribute()
    {
        if (!$this->sw_area) {
            return 0;
        }

        return $this->sw_cost / $this->sw_area;
    }

    function getBuildingCostPerM2Attribute()
    {
        if (!$this->building_area) {
            return 0;
        }

        return $this->building_cost / $this->building_area;
    }

    function getBuiltPricePerM2Attribute()
    {
        $total_area = $this->sw_area + $this->building_area;
        if (!$total_area) {
            return 0;
        }

        return $this->eac_contract_amount  / $total_area;
    }

    function getTotalBuiltCostPerM2Attribute()
    {
        $total_area = $this->sw_area + $this->building_area;
        if (!$total_area) {
            return 0;
        }

        return $this->budget_cost  / $total_area;
    }



}