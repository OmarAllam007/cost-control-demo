<?php

namespace App;

use App\Filter\BreakdownFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code', 'resource_qty', 'resource_qty_manual'];

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

    function resource()
    {
        return $this->belongsTo(StdActivityResource::class, 'std_activity_resource_id')->withTrashed();
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class)->withTrashed();
    }

    function getProjectResourceAttribute()
    {
        $resource = $this->resource->resource;

        if (!$resource) {
            return null;
        }

        $projectResource = Resources::where('resource_id', $resource->id)
            ->where('project_id', $this->breakdown->project->id)->first();
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
        if ($this->resource_qty_manual) {
            return $this->attributes['resource_qty'];
        }

        $v = $V = $this->budget_qty;

        $variables = [];
        foreach ($this->variables as $variable) {
            $variables["v{$variable->display_order}"] = $variable->value;
            $variables["V{$variable->display_order}"] = $variable->value;
        }
        extract($variables);

        $result = 0;
        @eval('$result=' . $this->resource->equation . ';');
        return $result;
    }

    function getBudgetUnitAttribute()
    {
        if ($this->productivity) {
            $reductionFactor = $this->project_productivity->reduction_factor;
            if (!$reductionFactor) {
                return 0;
            }

            $result = $this->resource_qty * $this->labor_count / $reductionFactor;
            return $result > 0.25 ? round($result, 2) : 0.25;
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
        $budgetQuantity = Survey::where('cost_account', $this->breakdown->cost_account)->first()->budget_qty;
        return $budgetQuantity;
    }

    function scopeFilter(Builder $query, $fields)
    {
        $filter = new BreakdownFilter($query, $fields);
        return $filter->filter();
    }

    function syncVariables($variables)
    {
        $qtySurvey = Survey::where('cost_account', $this->breakdown->cost_account)
            ->where('project_id', $this->breakdown->project_id)->first();

        $variableNames = $this->resource->variables->pluck('label', 'display_order');

        foreach ($variables as $index => $value) {
            $var = BreakdownVariable::where('qty_survey_id', $qtySurvey->id)->where('display_order', $index)->first();
            if ($var) {
                $var->update(compact('value'));
            } else {
                $this->variables()->create([
                    'qty_survey_id' => $qtySurvey->id,
                    'name' => $variableNames[$index],
                    'value' => $value,
                    'display_order' => $index,
                ]);
            }
        }
    }

    function variables()
    {
        return $this->hasMany(BreakdownVariable::class);
    }
}
