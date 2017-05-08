<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use App\Support\DuplicateProject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Collection $resources
 */
class Project extends Model
{
    use SoftDeletes, HasOptions, Tree, CachesQueries;
    use HasChangeLog;

    protected static $alias = 'Project';
    protected $ids = [];
    protected $depth = 1;
    protected $fillable = [
        'name',
        'project_code',
        'client_name',
        'project_location',
        'project_contract_value',
        'project_start_date',
        'project_duration',
        'description',
        'owner_id',
        'original_finished_date',
        'expected_finished_date',
        'project_contract_signed_value',
        'project_contract_budget_value',
        'change_order_amount',
        'direct_cost_material',
        'indirect_cost_general',
        'total_budget_cost',
        'cost_owner_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

    function getWbsTreeAttribute()
    {
        return $this->wbs_levels()->tree()->get();
    }

    function wbs_levels()
    {
        return $this->hasMany(WbsLevel::class);
    }

    function boqs()

    {
        $relation = $this->hasMany(Boq::class, 'project_id');
        $relation->orderBy('description');
        return $relation;
    }

    function breakdown_resources()
    {
        return $this->hasManyThrough(BreakdownResource::class, Breakdown::class);
    }

    function quantities()
    {
        return $this->hasMany(Survey::class);
    }


    function resources()
    {
        return $this->hasMany(Resources::class)->with('types');
    }

    function getProductivitiesAttribute()
    {
        $refs = $this->shadows()
//            ->select('id', 'productivity_ref')
            ->where('productivity_ref', '!=', '')->whereNotNull('productivity_ref')
            ->pluck('productivity_id')->unique()->filter();

//        dd(Productivity::whereIn('id', $refs)->with('units')->count());
        return Productivity::whereIn('id', $refs)->with('units')->get();
    }

    /*function getResourcesAttribute()
    {
        if (empty($this->projectResources)) {
            $this->projectResources = Resources::where('project_id', $this->id)->get();
        }

        return $this->projectResources;
    }*/

    function getActivities()//get ids of Activities
    {
        return $this->breakdowns()
            ->with('std_activity')->get()
            ->pluck('std_activity.id');
    }

    function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    function shadows()
    {
        return $this->hasMany(BreakDownResourceShadow::class, 'project_id');
    }

    function templates()
    {
        return $this->hasMany(BreakdownTemplate::class);
    }

    function getDivisions()
    {
        $divisions = $this->breakdowns()->with('wbs_level.parent', 'wbs_level.parent.parent','wbs_level.parent.parent.parent', 'std_activity.division')->get()->pluck('std_activity.division');
        $all = collect();
        $parents = collect();
        foreach ($divisions as $division) {
            $all->push($division->id);
            $parent = $division;
            while ($parent->parent_id && $parent->id != $parent->parent_id) {
                $parent = $parent->parent;
                $all->push($parent->id);
            }
            $parents->push($parent->id);
        }
        $all->unique();
        $parents->unique();
        return compact('all', 'parents');

    }

    function getProjectResources()
    {
        $br_down_ids = $this->breakdowns()->pluck('id')->toArray();
        $std_activity_resources = BreakdownResource::whereIn('breakdown_id', $br_down_ids)->pluck('std_activity_resource_id')->toArray();

        $resources_ids = StdActivityResource::whereIn('id', $std_activity_resources)->pluck('resource_id')->toArray();

        return $resources_ids;
    }


    function periods()
    {
        return $this->hasMany(Period::class);
    }

    function open_period()
    {
        return $this->periods()->where('is_open', true)->first();
    }

    function users()
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->withPivot([
                'budget', 'cost_control', 'reports', 'wbs', 'breakdown', 'breakdown_templates', 'resources', 'productivity', 'actual_resources', 'boq', 'qty_survey',
                'activity_mapping', 'resource_mapping', 'periods', 'remaining_unit_price', 'remaining_unit_qty', 'manual_edit', 'delete_resources'
            ])
            ->withTimestamps();
    }

    function getPermissionsAttribute()
    {
        $pivotFields = [
            'budget', 'cost_control', 'reports', 'wbs', 'breakdown', 'breakdown_templates', 'resources', 'productivity', 'actual_resources', 'boq', 'qty_survey',
            'activity_mapping', 'resource_mapping', 'periods', 'remaining_unit_price', 'remaining_unit_qty', 'manual_edit', 'delete_resources',
        ];

        return $this->users->map(function (User $user) use ($pivotFields) {
            $row = [
                'name' => $user->name,
                'user_id' => $user->id
            ];

            foreach ($pivotFields as $field) {
                $row[$field] = $user->pivot->getAttribute($field);
            }

            return $row;
        });
    }

    function cost_shadow()
    {
        $relation = $this->hasMany(CostShadow::class)->where('period_id', $this->open_period()->id);
        return $relation;
    }

    function getIsCostReadyAttribute()
    {
        return !is_null($this->open_period()) && ActivityMap::forProject($this)->exists();
    }

    function getTreeDepthAttribute()
    {
        $max = 0;
        $levels = WbsLevel::where('project_id', $this->id)->get();
        foreach ($levels as $level) {
            $parent = $level;
            while ($parent->parent) {
                $this->depth++;
                $parent = $parent->parent;
            }

            if ($max < $this->depth) {
                $max = $this->depth;
            }
            $this->depth = 0;

        }
        return $max + 1;
    }

    function owner()
    {
        return $this->belongsTo(User::class);
    }

    function cost_owner()
    {
        return $this->belongsTo(User::class);
    }

    function duplicate($newName)
    {
        $duplicator = new DuplicateProject($this);
        return $duplicator->duplicate($newName);
    }

    function getMaxPeriod()
    {
        return $this->periods()->where('is_open', false)->max('id');
    }

}