<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbsLevel extends Model
{
    use SoftDeletes;
    use Tree, HasOptions;

    protected $fillable = ['name', 'project_id', 'parent_id', 'comments', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return self::pluck('name', 'id')->prepend('Select Level', '');
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForProject(Builder $query, $project_id)
    {
        $query->where('project_id', $project_id);
    }

    public function deleteRecursive()
    {
        if ($this->children->count()) {
            $this->children->each(function ($level) {
                $level->deleteRecursive();
            });
        }

        $this->delete();
    }

    public function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    public function getBudgetCostAttribute()
    {
        $budget_cost = 0;
        $children = [];
        if ($this->children && count($this->children)) {
            $this->children->load('breakdowns', 'breakdowns.resources', 'breakdowns.resources.template_resource', 'breakdowns.resources.template_resource.resource');
            foreach ($this->children as $child) {
                $children [] = $child->id;
                if (count($child->breakdowns)) {
                    foreach ($child->breakdowns as $break_down) {
                        foreach ($break_down->resources as $resource) {
                            $budget_cost += $resource->budget_cost;
                        }
                    }
                }
            }
        }
        return ['budget_cost' => $budget_cost, 'children' => $children];
    }


    function getEngQty($request)
    {
        $eng_qty = 0;
        $wbs_level = WbsLevel::find($request->wbs_level_id);
        $survey_level = Survey::where('wbs_level_id', $wbs_level->id)->where('cost_Account', $request->cost_account)->first();
        if ($survey_level) {
            $eng_qty = $survey_level->eng_qty;
        }
        else {
            $parent = $wbs_level;
            while ($parent->parent) {
                $parent = $parent->parent;
//                $parent_wbs_level = WbsLevel::find($parent->id);
                $parent_survey = Survey::where('wbs_level_id', $parent->id)->where('cost_account', $request->cost_account)->first();
                if ($parent_survey) {
                    $eng_qty = $parent_survey->eng_qty;
                    break;
                }

            }
        }
        return $eng_qty;
    }
}