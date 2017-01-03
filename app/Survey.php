<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Formatters\BreakdownResourceFormatter;
use App\Http\Controllers\Caching\ResourcesCache;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use CachesQueries;

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
        foreach ($this->variables as $var) {
            $var->update(['value' => $variables[$var->id]]);
        }
    }

    public function updateBreakdownResources()
    {
        $variables = $this->variables->pluck('name')->toArray();

//        $shadows = BreakDownResourceShadow::whereIn('wbs_id',$this->wbsLevel->getChildrenIds())
//            ->where('cost_account',$this->cost_account)
//            ->where('project_id',$this->project_id)
//            ->whereIn('resource_name',$variables)->get();
//        dd($shadows);

        $resourceIds = Resources::whereIn('name', $variables)->pluck('id')->toArray();
        $breakdowns = Breakdown::where('project_id', $this->project_id)
            ->whereIn('wbs_level_id', $this->wbsLevel->getChildrenIds())
            ->where('cost_account', $this->cost_account)->get();

        foreach ($breakdowns as $breakdown) {
            $breakdown_resources = BreakdownResource::where('breakdown_id', $breakdown->id)
                ->whereIn('resource_id', $resourceIds)->get();

            foreach ($breakdown_resources as $breakdown_resource) {
                $formatter = new BreakdownResourceFormatter($breakdown_resource);
                BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)
                    ->update($formatter->toArray());
            }
        }
//        $cache = new ResourcesCache();
//        $cache->cacheResources();
    }

}