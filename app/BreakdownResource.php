<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Filter\BreakdownFilter;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    use HasChangeLog;

    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level_id', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code', 'resource_qty', 'resource_id', 'equation'];

    protected $calculated_resource_qty;

    protected $productivity_cache;

    protected $resource_cache;

//    public $original_resource = 0;

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

    function resource()
    {
        return $this->belongsTo(Resources::class)->withTrashed();
    }

    function getEquationAttribute()
    {
        if (!empty($this->attributes['equation'])) {
            return $this->attributes['equation'];
        }

        return $this->template_resource->equation;
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

        if (!trim($this->equation)) {
            return 0;
        }

        $v = $V = $this->budget_qty;

        $variables = [];

        if ($this->qty_survey) {
            foreach ($this->qty_survey->variables as $variable) {
                $variables["v{$variable->display_order}"] = $variable->value ?: 0;
                $variables["V{$variable->display_order}"] = $variable->value ?: 0;
            }
        }
        extract($variables);
        $result = 0;
        try {
            $equation = '$result = ' . $this->equation . ';';
//            if (!check_syntax($equation)) {
//                \Log::warning($this->toJSON());
//                return $this->calculated_resource_qty = 0;
//            }
            $eval = @eval($equation);
            if ($eval === false || !$result || $result == INF || is_nan($result)) {
                $result = 0;
            }

            return $this->calculated_resource_qty = $result;
        } catch (\Exception $e) {
            \Log::warning($this);
            return $this->calculated_resource_qty = 0;
        }
    }

    function getQtySurveyAttribute()
    {
        $finalSurvey = '';
        $level = WbsLevel::where('id', $this->wbs_level_id)->first();
        $survey = Survey::where('cost_account', $this->cost_account)->where('wbs_level_id', $level->id)
            ->where('project_id', $this->breakdown->project_id)
            ->first();
        if(!$survey){
            $parent = $level;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parentsurvey = Survey::where('cost_account', $this->cost_account)->where('wbs_level_id', $parent->id)
                    ->where('project_id', $this->breakdown->project_id)
                    ->first();
                if ($parentsurvey) {
                    $finalSurvey = $parentsurvey;
                    break;
                }
            }
        }
        else{
            $finalSurvey = $survey;
        }

        return $finalSurvey;

    }

    function getBudgetUnitAttribute()
    {
        if (!floatval($this->budget_qty)) {
            return 0;
        }

        $resourceQty = $this->resource_qty;
        if ($this->productivity) {
            $reductionFactor = $this->project_productivity->after_reduction;
            if (!$reductionFactor) {
                return 0;
            }

            $result = $resourceQty * $this->labor_count / $reductionFactor;
            return $result > 0.25 ? round($result + 0.05, 1) : 0.25;
        } else {
            return $resourceQty * (1 + ($this->resource_waste / 100));
        }
    }

    function getBudgetCostAttribute()
    {
        if (isset($this->resource->rate)) {
            return $this->budget_unit * $this->resource->rate;
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

//    function getOriginalResourceIdAttribute()
//    {
//        if ($this->original_resource == 0) {
//            $this->original_resource = $this->getOriginal('resource_id');
//        }
//        return $this->original_resource;
//    }
//
//    function getEngQtyAttribute()
//    {
//
//        $wbs_level = WbsLevel::find($this->breakdown->wbs_level->id);
//        $survey_level = Survey::where('wbs_level_id', $wbs_level->id)->where('cost_Account', $this->breakdown->cost_account)->first();
//        $eng_qty = 0;
//        if ($survey_level) {
//            $eng_qty = $survey_level->eng_qty;
//        } else {
//            $parent = $wbs_level;
//            while ($parent->parent) {
//                $parent_wbs_level = WbsLevel::find($parent->id);
//                $parent_survey = Survey::where('wbs_level_id', $parent_wbs_level->id)->where('cost_Account', $this->breakdown->cost_account)->first();
//                if ($parent_survey) {
//                    $eng_qty = $parent_survey->eng_qty;
//                    break;
//                }
//                $parent = $parent->parent;
//            }
//        }
//        return $eng_qty;
////        return $engQuantity;
//    }

//    function getBudgetQtyAttribute()
//    {
//        $eng_qty = 0;
//        $survey_level = Survey::where('wbs_level_id', $this->breakdown->wbs_level_id)->where('cost_account', $this->breakdown->cost_account)->first();
//        if ($survey_level) {
//            $eng_qty = $survey_level->budget_qty;
//        } else {
//            $parent = $this;
//            while ($parent->parent) {
//                $parent = $parent->parent;
//                $parent_survey = Survey::where('wbs_level_id', $parent->id)
//                    ->where('cost_account', $this->breakdown->cost_account)->first();
//                if ($parent_survey) {
//                    $eng_qty = $parent_survey->budget_qty;
//                    break;
//                }
//
//            }
//        }
//        return $eng_qty;
//    }

    function scopeFilter(Builder $query, $fields)
    {
        $filter = new BreakdownFilter($query, $fields);
        return $filter->filter();
    }

    function scopeForWbs(Builder $query, $wbs_id)
    {

        return $query->whereHas('breakdown', function (Builder $q) use ($wbs_id) {
            return $q->where('wbs_level_id', $wbs_id);
        });
    }

    function shadow()
    {
        return $this->hasOne(BreakDownResourceShadow::class);
    }

    public function updateShadow()
    {
        $formatter = new BreakdownResourceFormatter($this);
        $shadow = BreakDownResourceShadow::firstOrCreate(['breakdown_resource_id' => $this->id]);
        $shadow->update($formatter->toArray());
    }
}
