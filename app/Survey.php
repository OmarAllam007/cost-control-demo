<?php

namespace App;

use App\Behaviors\CachesQueries;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use CachesQueries;

    protected $table = 'qty_surveys';

    protected $fillable = ['unit_id','budget_qty','eng_qty','cost_account','category_id','description','wbs_level_id','project_id','code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function checkImportData($data)
    {
        $errors = [];

        foreach ($data['units'] as $unit => $unit_id) {
            if (empty($unit_id)) {
                $errors['units.'.$unit] = $unit;
            }
        }

        foreach ($data['wbs'] as $wbs => $wbs_id) {
            if (empty($wbs_id)) {
                $errors['wbs.'.$wbs] = $wbs;
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

}