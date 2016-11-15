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

    public function getBudgetCostAttribute()
    {
        $budget_cost = 0;
        $children = [];
        if ($this->children && count($this->children)) {
            foreach ($this->children as $child) {
                $child_break_downs = Breakdown::where('wbs_level_id', $child->id)->get();
                $children [] = $child->id;
                if ($child_break_downs) {
                    foreach ($child_break_downs as $break_down) {
                        $child_break_down_resources = $break_down->resources;
                        foreach ($child_break_down_resources as $resource) {
                            $budget_cost += $resource->budget_cost;
                        }
                    }
                }
            }
        }
        return ['budget_cost'=>$budget_cost,'children'=>$children];
    }
}