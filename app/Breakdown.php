<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breakdown extends Model
{
    protected $fillable = ['std_activity_id', 'template_id', 'name', 'cost_account', 'project_id', 'wbs_level_id', 'code'];

    function resources()
    {
        return $this->hasMany(BreakdownResource::class, 'breakdown_id');
    }

    function wbs_level()
    {
        return $this->belongsTo(WbsLevel::class)->withTrashed();
    }

    function std_activity()
    {
        return $this->belongsTo(StdActivity::class);
    }

    function template()
    {
        return $this->belongsTo(BreakdownTemplate::class, 'template_id')->withTrashed();
    }

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    /*function syncResources($resources)
    {
        foreach ($resources as $res) {
            $resource = $this->resources()->create($res);
            if (!empty($res['variables'])) {
                $resource->syncVariables($res['variables']);
            }
        }
    }*/

    function syncVariables($variables)
    {
        $qtySurvey = Survey::where('cost_account', $this->cost_account)->where('project_id', $this->project_id)->first();
        $variableNames = $this->std_activity->variables->pluck('label', 'display_order');
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
