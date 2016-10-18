<?php

namespace App;

use App\Filter\BreakdownFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code'];

    function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
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
        $v = $this->budget_qty;
        $V = $this->budget_qty;
        $result = '';
        eval('$result=' . $this->resource->equation . ';');
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

    function getEngQuantityAttribute()
    {
        $engQuantity = Survey::where('cost_account', $this->breakdown->cost_account)->first()->eng_qty;
        return $engQuantity;
    }
    function getBudgetQuantityAttribute()
    {
        $engQuantity = Survey::where('cost_account', $this->breakdown->cost_account)->first()->budget_qty;
        return $engQuantity;
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
            $this->variables()->create([
                'qty_survey_id' => $qtySurvey->id,
                'name' => $variableNames[$index],
                'value' => $value,
                'display_order' => $index,
            ]);
        }
    }

    function variables()
    {
        return $this->hasMany(BreakdownVariable::class);
    }
}
