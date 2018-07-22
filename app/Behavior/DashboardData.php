<?php

namespace App\Behavior;

use App\MasterShadow;
use Carbon\Carbon;

trait DashboardData
{
    function getActualTimeProgressAttribute()
    {
//        if (!empty($this->attributes['actual_progress'])) {
//            return $this->attributes['actual_progress'];
//        }

        if ($this->expected_duration) {
            return round(min(100,$this->time_elapsed * 100 / $this->expected_duration), 2);
        }

        return '';
    }

    function getStartDateAttribute()
    {
        if ($this->global_period) {
            return $this->global_period->start_date;
        }

        return Carbon::parse($this->attributes['start_date']);
    }

    function getEndDateAttribute()
    {
        if ($this->global_period) {
            return $this->global_period->end_date;
        }

        return Carbon::parse($this->attributes['end_date']);
    }

    function getAtCompletionCostAttribute()
    {
        return MasterShadow::where('period_id', $this->id)->sum('completion_cost');
    }

    function getEacProfitAttribute()
    {
        if (!empty($this->chached_eac_profit)) {
            return  $this->chached_eac_profit;
        }

        $reserve = MasterShadow::where('period_id', $this->id)->where('activity_id', 3060)->value('budget_cost');

        $completion_cost = $this->at_completion_cost - $reserve;
        return $this->chached_eac_profit = $this->contract_value - $completion_cost;
    }

    function getEacProfitabilityIndexAttribute()
    {
        return $this->eac_profit * 100 / $this->contract_value;
    }

    function getActualDurationAttribute()
    {
        $actual_finish = Carbon::parse($this->forecast_finish_date);
        $actual_start = Carbon::parse($this->project->actual_start_date);

        return $actual_finish->diffInDays($actual_start);
    }

    function getDurationVarianceAttribute()
    {
        if ($this->attributes['duration_variance']) {
            return $this->attributes['duration_variance'];
        }

        return $this->expected_duration - $this->actual_duration;
    }

    function getAllowableCostForReportsAttribute()
    {
        return MasterShadow::where('period_id', $this->id)->sum('allowable_ev_cost') + $this->to_date_management_reserve;
    }

    function getToDateCostForReportsAttribute()
    {
        return MasterShadow::where('period_id', $this->id)->sum('to_date_cost');
    }

    function getToDateManagementReserveAttribute()
    {
        if ($this->to_date_reserve) {
            return $this->to_date_reserve;
        }

        $budget_cost = MasterShadow::where('period_id', $this->id)->sum('budget_cost');
        $reserve_budget = MasterShadow::where('period_id', $this->id)->where('activity_id', 3060)->sum('budget_cost');
        $net_budget = $budget_cost - $reserve_budget;
        $progress = min(1, $this->to_date_cost_for_reports / $net_budget);
        return $this->to_date_reserve = $progress * $reserve_budget;
    }

    function getPlannedFinishDateAttribute()
    {
        if ($this->attributes['planned_finish_date']) {
            return Carbon::parse($this->attributes['planned_finish_date']);
        }

        return Carbon::parse($this->project->original_finish_date);
    }

    function getContractValueAttribute()
    {
        return $this->change_order_amount + $this->project->project_contract_signed_value;
    }

    function getExpectedDurationAttribute()
    {
        if (!empty($this->attributes['expected_duration'])) {
            return $this->attributes['expected_duration'];
        }

        if (!empty($this->project->project_start_date) && !empty($this->planned_finish_date)) {
            $actual_start = Carbon::parse($this->project->project_start_date);
            $expected_finish = Carbon::parse($this->planned_finish_date);

            return $expected_finish->diffInDays($actual_start);
        }

        return $this->project->project_duration;
    }

    function getTimeExtensionAttribute()
    {
        if (!empty($this->attributes['time_extension'])) {
            return $this->attributes['time_extension'];
        }

        return intval($this->expected_duration) - intval($this->project->project_duration);
    }

    function getTimeRemainingAttribute()
    {
        if (!empty($this->attributes['time_remaining'])) {
            return $this->attributes['time_remaining'];
        }

        if ($this->actual_duration) {
            return $this->actual_duration - $this->time_elapsed;
        }

        return '';
    }

    function getPlannedValueAttribute()
    {
        if (!empty($this->attributes['planned_value']) && $this->attributes['planned_value'] != 0) {
            return $this->attributes['planned_value'];
        }

        // Planned Value = Planned Progress * Revised contract amount
        return $this->planned_progress * $this->contract_value / 100;
    }

    function getEarnedValueAttribute()
    {
        if (!empty($this->attributes['earned_value']) && $this->attributes['earned_value'] != 0) {
            return $this->attributes['earned_value'];
        }

        // Earned Value = Actual Progress * Revised contract amount
        return $this->actual_progress * $this->contract_value / 100;
    }

    function getSpiIndexAttribute()
    {
        if ($this->attributes['spi_index']) {
            return $this->attributes['spi_index'];
        }

        // SPI Index = Earned Value / Planned Value
        if (!$this->planned_value) {
            return 0;
        }

        return $this->earned_value / $this->planned_value;
    }

    function getTimeElapsedAttribute()
    {
        if ($this->attributes['time_elapsed']) {
            return $this->attributes['time_elapsed'];
        }

        // Time Elapsed = Period Finish Date - Project Actual Start
        $start_date = Carbon::parse($this->project->actual_start_date);
        return $this->end_date->diffInDays($start_date);
    }
}