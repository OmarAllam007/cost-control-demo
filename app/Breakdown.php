<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

/**
 * @property WbsLevel $wbs_level
 * @property Survey qty_survey
 */
class Breakdown extends Model
{
//    use CachesQueries;
    use HasChangeLog;

    protected $fillable = ['std_activity_id', 'template_id', 'name', 'cost_account', 'project_id', 'wbs_level_id', 'code'];
    protected $cached_qty_survey;

    function resources()
    {
        return $this->hasMany(BreakdownResource::class, 'breakdown_id');
    }

    function shadows()
    {
        return $this->hasMany(BreakDownResourceShadow::class, 'breakdown_id');
    }

    function wbs_level()
    {
        return $this->belongsTo(WbsLevel::class);
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

    /*function qty_survey()
    {
        return $this->belongsTo(Survey::class, 'cost_account', 'cost_account')->where('project_id', $this->project_id)->where('wbs_level_id', $this->wbs_level_id);
    }*/

    function getQtySurveyAttribute()
    {
        if ($this->cached_qty_survey) {
            return $this->cached_qty_survey;
        }

        $qtySurvey = Survey::where('cost_account', $this->cost_account)->where('project_id', $this->project_id)->where('wbs_level_id', $this->wbs_level_id)->first();
        if ($qtySurvey) {
            return $qtySurvey;
        }

        $parent = $this->wbs_level;
        while ($parent->parent) {
            $parent = $parent->parent;
            $qtySurvey = Survey::where('cost_account', $this->cost_account)->where('project_id', $this->project_id)->where('wbs_level_id', $parent->id)->first();
            if ($qtySurvey) {
                return $this->cached_qty_survey = $qtySurvey;
            }
        }

        return null;
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
        if ($variables && $this->qty_survey) {
            $variableNames = $this->std_activity->variables->pluck('label', 'display_order');
            foreach ($variables as $index => $value) {
                $var = BreakdownVariable::where('qty_survey_id', $this->qty_survey->id)->where('display_order', $index)->first();
                if ($var) {
                    $var->update(compact('value'));
                } else {
                    $this->variables()->create([
                        'qty_survey_id' => $this->qty_survey->id,
                        'name' => $variableNames->get($index, ''),
                        'value' => $value,
                        'display_order' => $index,
                    ]);
                }
            }
        }
    }

    function variables()
    {
        return $this->hasMany(BreakdownVariable::class);
    }

    public function duplicate($data)
    {
        $newData = $this->getAttributes();
        unset($newData['id'], $newData['created_at']);
        $newData['wbs_level_id'] = $data['wbs_level_id'];
        $newData['cost_account'] = $data['cost_account'];
        $newBreakdown = self::create($newData);

        $qty_survey = Survey::where('cost_account', $newData['cost_account'])
            ->where('wbs_level_id', $newData['wbs_level_id'])
            ->select('id', 'budget_qty', 'eng_qty')->first();

        foreach ($this->resources as $resource) {
            $newResource = $resource->getAttributes();
            if ($qty_survey) {
                $newResource['budget_qty'] = $qty_survey->budget_qty;
                $newResource['eng_qty'] = $qty_survey->eng_qty;
            } else {
                $newResource['budget_qty'] = 0;
                $newResource['eng_qty'] = 0;
            }

            unset($newResource['id'], $newResource['breakdown_id'], $newResource['created_at']);
            $newBreakdown->resources()->create($newResource);
        }

        if ($qty_survey) {
            $qty_survey_id = $qty_survey->id;
            foreach ($this->variables as $var) {
                $newVar = $var->toArray();
                unset($var['id'], $var['breakdown_id'], $var['created_at'], $var['updated_at']);
                $newVar['qty_survey_id'] = $qty_survey_id;
                $newBreakdown->variables()->create($newVar);
            }
        }

        return $newBreakdown;
    }

    function getDry($project, $wbs_id, $cost_account)
    {
        $boq = Boq::where('wbs_id', $wbs_id)->where('project_id', $project->id)->where('cost_account', $cost_account)->first();
        if (isset($boq->dry_ur)) {
            return $boq->dry_ur;
        }
        return 0;
    }
}
