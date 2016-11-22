<?php

namespace App;

use App\Filter\BreakdownFilter;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code', 'resource_qty', 'resource_id', 'equation'];

    protected $calculated_resource_qty;

    protected $productivity_cache;
    protected $resource_cache;

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
        if (isset($this->attributes['equation'])) {
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
        if ($this->resource_cache) {
            return $this->resource_cache;
        }

        if (isset($this->attributes['resource_id'])) {
            $resource = Resources::find($this->attributes['resource_id']);
        } else {
            $resource = $this->template_resource->resource;
        }

        $projectResource = Resources::where('resource_id', $resource->id)->where('project_id', $this->breakdown->project->id)->first();
        if ($projectResource) {
            return $this->resource_cache = $projectResource;
        }

        return $this->resource_cache = $resource;
    }

    function getProjectProductivityAttribute()
    {
        if (!empty($this->productivity_cache)) {
            return $this->productivity_cache;
        }

        $productivity = $this->productivity;

        if ($productivity) {
            $projectProductivity = Productivity::where('productivity_id', $productivity->id)
                ->where('project_id', $this->breakdown->project->id)->first();
            if ($projectProductivity) {
                return $this->productivity_cache = $projectProductivity;
            }

            return $this->productivity_cache = $productivity;
        }

        return null;
    }

    function getResourceQtyAttribute()
    {
        if (isset($this->calculated_resource_qty)) {
            return $this->calculated_resource_qty;
        }

        if (!$this->equation) {
            return 0;
        }

        $v = $V = $this->budget_qty;

        $variables = [];
        if ($this->qty_survey && $this->qty_survey->variables->count()) {
            foreach ($this->qty_survey->variables as $variable) {
                $variables["v{$variable->display_order}"] = $variable->value ?: 0;
                $variables["V{$variable->display_order}"] = $variable->value ?: 0;
            }
        }
        extract($variables);

        $result = 0;
        $eval = @eval('$result=' . $this->equation . ';');
        if ($eval === false || !$result || $result == INF || is_nan($result)) {
            $result = 0;
        }
        return $this->calculated_resource_qty = $result;
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
        $costAccount = Survey::where('cost_account', $this->breakdown->cost_account)->first();
        $engQuantity = 0;
        if ($costAccount) {
            $engQuantity = $costAccount->eng_qty;
        }
        return $engQuantity;
    }

    function getBudgetQtyAttribute()
    {
        $costAccount = Survey::where('cost_account', $this->breakdown->cost_account)->first();
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

        return $query->whereHas('breakdown', function(Builder $q) use ($wbs_id){
            return $q->where('wbs_level_id', $wbs_id);
        });
    }

    function toArray()
    {
        return [
            'breakdown_resource_id' => $this->id,
            'project_id' =>$this->breakdown->project->id,
            'wbs_id' =>$this->breakdown->wbs_level->id,
            'breakdown_id' => $this->breakdown->id,
            'template' => $this->breakdown->template->name,
            'activity' => $this->breakdown->std_activity->name,
            'activity_id' => $this->breakdown->std_activity->id,
            'cost_account' => $this->breakdown->cost_account,
            'eng_qty' => number_format($this->eng_qty, 2),
            'budget_qty' => number_format($this->budget_qty, 2),
            'resource_qty' => number_format($this->resource_qty, 2),
            'resource_waste' => $this->resource_waste,
            'resource_type' => $this->resource->types->root->name,
            'resource_type_id' => $this->resource->types->root->id,
            'resource_code' => $this->resource->resource_code,
            'resource_name' => $this->resource->name,
            'unit_price' => number_format($this->resource->rate, 2),
            'measure_unit' => $this->resource->units->type,
            'budget_unit' => number_format($this->budget_unit, 2),
            'budget_cost' => number_format($this->budget_cost, 2),
            'boq_equivilant_rate' => number_format($this->boq_unit_rate, 2),
            'labors_count' => !empty($this->labor_count) ? $this->labor_count : '',
            'productivity_output' => isset($this->project_productivity->after_reduction) ? $this->project_productivity->after_reduction : '',
            'productivity_ref' => isset($this->project_productivity->csi_code) ? $this->project_productivity->csi_code : '',
            'remarks' => $this->resource->remarks,
        ];
    }



}
