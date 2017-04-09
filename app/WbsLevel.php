<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use App\Jobs\CacheWBSTree;
use App\Jobs\CacheWBSTreeInQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbsLevel extends Model
{
    use SoftDeletes;
    use Tree, HasOptions;
    use CachesQueries;
    use HasChangeLog;

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
        $this->deleteRelations();
        $this->delete();
    }

    function deleteRelations()
    {
        $ids = $this->getChildrenIds();
        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
        BreakdownVariable::flushEventListeners();

        $breakdown_ids = Breakdown::whereIn('wbs_level_id', $ids)->pluck('id');
        $breakdown_resource_ids = BreakdownResource::whereIn('breakdown_id', $breakdown_ids)->pluck('id');
        Breakdown::whereIn('id', $breakdown_ids)->delete();
        BreakdownResource::whereIn('breakdown_id', $breakdown_ids)->delete();
        BreakDownResourceShadow::whereIn('breakdown_resource_id', $breakdown_resource_ids)->delete();
        BreakdownVariable::whereIn('breakdown_id', $breakdown_ids);
        Boq::whereIn('wbs_id', $ids)->delete();
        Survey::whereIn('wbs_level_id', $ids)->delete();
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

    public function deepCopy($project_id, $parent_id = 0)
    {

    }


    function getDry(){
        $boq = Boq::where('wbs_id',$this->id)->first();
        if(isset($boq->dry_ur)){//chenged from $boq->dry_ur to 1
            return $boq->dry_ur;
        }
        return 0;
    }

    function getParents()
    {
        $parents = collect([$this->name]);
        $parent = $this->parent;
        while ($parent) {
            $parents->push($parent->name);
            $parent = $parent->parent;
        }
        return $parents->reverse();
    }
}