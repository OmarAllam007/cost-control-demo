<?php

namespace App;

use App\Filter\BreakdownFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code', 'resource_qty', 'resource_id', 'equation'];

    function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
    }

    function getWbsLevelIdAttribute()
    {
        $this->load(['breakdown']);
        return $this->breakdown->wbs_level_id;
    }

    function getCostAccountAttribute()
    {
        $this->load(['breakdown']);
        return $this->breakdown->cost_account;
    }

    function getStdActivityIdAttribute()
    {
        $this->load(['breakdown']);
        return $this->breakdown->std_activity_id;
    }

    function template_resource()
    {
        return $this->belongsTo(StdActivityResource::class, 'std_activity_resource_id')->withTrashed();
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class)->withTrashed();
    }

    function getResourceIdAttribute()
    {
        return $this->resource->id;
    }

    function getEquationAttribute()
    {
        if ($this->attributes['equation']) {
            return $this->attributes['equation'];
        }

        return $this->template_resource->equation;
    }

    function getResourceAttribute()
    {
        return $this->project_resource;
    }

    function getProjectResourceAttribute()
    {
        if ($this->attributes['resource_id']) {
            $resource = Resources::find($this->attributes['resource_id']);
        } else {
            $resource = $this->template_resource->resource;
        }

        $projectResource = Resources::where('resource_id', $resource->id)->where('project_id', $this->breakdown->project->id)->first();
        if ($projectResource) {
            return $projectResource;
        }

        return $resource;
    }

    function getProjectProductivityAttribute()
    {
        $productivity = $this->productivity;

        if ($productivity) {
            $projectProductivity = Productivity::where('productivity_id', $productivity->id)
                ->where('project_id', $this->breakdown->project->id)->first();
            if ($projectProductivity) {
                return $projectProductivity;
            }

            return $productivity;
        }

        return null;
    }

    function getResourceQtyAttribute()
    {
//        if ($this->resource_qty_manual) {
//            return $this->attributes['resource_qty'];
//        }
        if (!$this->equation) {
            return 0;
        }

        $v = $V = $this->budget_qty;

        if ($this->qty_survey) {

            $variables = [];
            foreach ($this->qty_survey->variables as $variable) {
                $variables["v{$variable->display_order}"] = $variable->value ?: 0;
                $variables["V{$variable->display_order}"] = $variable->value ?: 0;
            }
            extract($variables);
        }

        $result = 0;
        @eval('$result=' . $this->equation . ';');
        return $result;
    }

    function getQtySurveyAttribute()
    {
        return Survey::where('cost_account', $this->cost_account)
            ->where('project_id', $this->breakdown->project_id)
            ->first();
    }

    function getBudgetUnitAttribute()
    {
        if ($this->productivity) {
            $reductionFactor = $this->project_productivity->after_reduction;
            if (!$reductionFactor) {
                return 0;
            }

            $result = $this->resource_qty * $this->labor_count / $reductionFactor;
            return $result > 0.25 ? round($result + 0.05, 1) : 0.25;
        } else {
            return $this->resource_qty * (1 + ($this->resource_waste / 100));
        }
    }

    function getBudgetCostAttribute()
    {
        if (isset($this->project_resource->rate)) {
            return $this->budget_unit * $this->project_resource->rate;
        }

        return 0;
    }

    function getBoqUnitRateAttribute()
    {
        if (!$this->eng_qty) {
            return 0;
        }
        return $this->budget_cost / $this->eng_qty;
    }

    function getEngQtyAttribute()
    {
        $costAccount = $this->breakdown->qty_survey;

        $engQuantity = 0;
        if ($costAccount) {
            $engQuantity = $costAccount->eng_qty;
        }
        return $engQuantity;
    }

    function getBudgetQtyAttribute()
    {
        $costAccount = $this->breakdown->qty_survey;
        $budgetQuantity = 0;
        if ($costAccount) {
            $budgetQuantity = $costAccount->budget_qty;
        }
        return $budgetQuantity;
    }

    function scopeFilter(Builder $query, $fields)
    {
        $filter = new BreakdownFilter($query, $fields);
        return $filter->filter();
    }

    function scopeForWbs(Builder $query, $wbs_id) {
        return $query->with(['breakdown', 'breakdown.template', 'breakdown.std_activity'])->whereHas('breakdown', function(Builder $q) use ($wbs_id){
            return $q->where('wbs_level_id', $wbs_id);
        });
    }
}
