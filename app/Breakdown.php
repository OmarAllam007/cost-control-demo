<?php

namespace App;

use App\Behaviors\CachesQueries;
use Illuminate\Database\Eloquent\Model;

class Breakdown extends Model
{
    use CachesQueries;

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

    function qty_survey()
    {
        return $this->belongsTo(Survey::class, 'cost_account', 'cost_account')->where('project_id', $this->project_id);
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

        if ($variables) {
            $qtySurvey = Survey::where('cost_account', $this->cost_account)->where('project_id', $this->project_id)->where('wbs_level_id', $this->wbs_level_id)->first();
            if(!$qtySurvey){
                $parent = $this->wbs_level;
                while($parent->parent){
                    $parent = $parent->parent;
                    $qtySurvey = Survey::where('cost_account', $this->cost_account)->where('project_id', $this->project_id)->where('wbs_level_id', $parent->id)->first();
                    if($qtySurvey){
                        break;
                    }

                }
            }
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
    }

    function variables()
    {
        return $this->hasMany(BreakdownVariable::class);
    }

    public function duplicate($data)
    {
        $newData = $this->toArray();
        unset($newData['id'], $newData['created_at']);
        $newData['wbs_level_id'] = $data['wbs_level_id'];
        $newData['cost_account'] = $data['cost_account'];
        $newBreakdown = self::create($newData);

        foreach ($this->resources as $resource) {

            $newResource = $resource->toArray();
            unset($newResource['id'], $newResource['breakdown_id'], $newResource['created_at']);
            $newBreakdown->resources()->create($newResource);
        }

        $qty_survey_id = $newBreakdown->qty_survey->id;
        foreach ($this->variables as $var) {
            $newVar = $var->toArray();
            unset($var['id'], $var['breakdown'], $var['created_at'], $var['updated_at']);
            $var['qty_survey_id']= $qty_survey_id;
            $newBreakdown->variables()->create($newVar);
        }

        return $newBreakdown;
    }

    function getDry($wbs_id){
        $boq = Boq::where('wbs_id',$wbs_id)->first();
        if(isset($boq->dry_ur)){
            return $boq->dry_ur;
        }
        return 0;
    }
}
