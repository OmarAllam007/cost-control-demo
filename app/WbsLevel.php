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

        $this->breakdowns()->delete();
        Boq::where('wbs_level_id', $this->id)->delete();
        Survey::where('wbs_level_id', $this->id)->delete();
        $this->delete();
    }

    public function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    public function getBudgetCostAttribute()
    {

        $children = [];
        $children =$this->getChildrenIds();
        $budget_cost = BreakDownResourceShadow::whereIn('wbs_id', $this->getChildrenIds())->sum('budget_cost');
        return ['budget_cost' => $budget_cost, 'children' => $children];
    }


    function getEngQty($cost_account)
    {
        $eng_qty = 0;
        $survey_level = Survey::where('wbs_level_id', $this->id)->where('cost_account', $cost_account)->first();
        if ($survey_level) {
            $eng_qty = $survey_level->eng_qty;
        } else {
            $parent = $this;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parent_survey = Survey::where('wbs_level_id', $parent->id)
                    ->where('cost_account', $cost_account)->first();
                if ($parent_survey) {
                    $eng_qty = $parent_survey->eng_qty;
                    break;
                }

            }
        }
        return $eng_qty;
    }

    function getBudgetQty($cost_account)
    {
        $budget_qty = 0;
        $survey_level = Survey::where('wbs_level_id', $this->id)->where('cost_account', $cost_account)->first();
        if ($survey_level) {
            $budget_qty = $survey_level->budget_qty;
        } else {
            $parent = $this;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parent_survey = Survey::where('wbs_level_id', $parent->id)
                    ->where('cost_account', $cost_account)->first();
                if ($parent_survey) {
                    $budget_qty = $parent_survey->budget_qty;
                    break;
                }

            }
        }
        return $budget_qty;
    }

    function getChildrenIdAttribute()
    {
        $children = collect();
        if ($this->children()->count()) {
            foreach ($this->children as $fChild) {
                $children->push($fChild->id);
                foreach ($fChild->children as $sChild) {
                    $children->push($sChild->id);
                    foreach ($sChild->children as $tChild) {
                        $children->push($tChild->id);
                    }
                }
            }
        }
        return $children->toArray();
    }

    function getCostAccountCheck($wbs_level, $cost_account)
    {
        $cost_accounts = collect();
        Survey::where('wbs_level_id', $wbs_level->id)->get()->each(function ($survey) use ($cost_accounts) {
            $cost_accounts->push($survey->cost_account);
        })->pluck('cost_account');

        $parent = $wbs_level;
        while ($parent->parent) {
            $parent = $parent->parent;
            Survey::where('wbs_level_id', $parent->id)->get()->each(function ($survey) use ($cost_accounts) {
                $cost_accounts->push($survey->cost_account);
            })->pluck('cost_account');

        }
        if (in_array($cost_account, $cost_accounts->toArray())) {
            return true;
        } else {
            return false;
        }
    }

    public function getRootAttribute()
    {
        $this->load(['parent', 'parent.parent', 'parent.parent.parent']);
        $parent = $this;
        while ($parent->parent_id && $parent->id != $parent->parent_id) {
            $parent = $parent->parent;
        }

        return $parent;
    }
}