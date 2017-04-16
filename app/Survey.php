<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Formatters\BreakdownResourceFormatter;
use App\Http\Controllers\Caching\ResourcesCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use CachesQueries;
    use HasChangeLog;

    protected $table = 'qty_surveys';

    protected $fillable = ['unit_id', 'budget_qty', 'eng_qty', 'cost_account', 'category_id', 'description', 'wbs_level_id', 'project_id', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function checkImportData($data)
    {
        $errors = [];

        foreach ($data['units'] as $unit => $unit_id) {
            if (empty($unit_id)) {
                $errors['units.' . $unit] = $unit;
            }
        }

        foreach ($data['wbs'] as $wbs => $wbs_id) {
            if (empty($wbs_id)) {
                $errors['wbs.' . $wbs] = $wbs;
            }
        }

        return $errors;
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function wbsLevel()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_level_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    function variables()
    {
        return $this->hasMany(BreakdownVariable::class, 'qty_survey_id');
    }

    function syncVariables($variables)
    {
        $breakdown_ids = [];
        foreach ($this->variables as $var) {
            $var->update(['value' => $variables[$var->id]]);
            $breakdown_ids[] = $var->breakdown_id;
        }

        // Make sure those breakdowns exist.
        $breakdown_ids = Breakdown::whereIn('id', $breakdown_ids)->pluck('id');

        $resources = BreakdownResource::whereIn('breakdown_id', $breakdown_ids)->get();
        /** @var BreakdownResource $resource */
        foreach ($resources as $resource) {
            $resource->updateShadow();
        }
    }

    public function updateBreakdownResources()
    {
        $breakdowns = Breakdown::where('project_id', $this->project_id)
            ->whereIn('wbs_level_id', $this->wbsLevel->getChildrenIds())
            ->where('cost_account', $this->cost_account)
            ->pluck('id');

        BreakdownResource::whereIn('breakdown_id', $breakdowns)->each(function (BreakdownResource $breakdown_resource) {
            $breakdown_resource->updateShadow();
        });
    }

    function scopeCostAccountOnWbs(Builder $query, WbsLevel $wbs, $cost_account)
    {
        $wbs_parents = collect($wbs->id);
        $parent = $wbs;
        while ($parent->parent_id) {
            $wbs_parents->push($parent->parent_id);
            $parent = $parent->parent;
        }

        $query->whereIn('wbs_level_id', $wbs_parents)->where('cost_account', $cost_account);

        return $query;
    }

}