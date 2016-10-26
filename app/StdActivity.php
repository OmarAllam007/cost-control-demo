<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;

class StdActivity extends Model
{
    use HasOptions;
    protected $orderBy = ['name','code'];
    protected static $alias = 'Activity';

    protected $fillable = ['code', 'name', 'division_id', 'work_package_name','id_partial','discipline'];

    protected $dates = ['created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(ActivityDivision::class, 'division_id');
    }

    public function breakdowns()
    {
        return $this->hasMany(BreakdownTemplate::class);
    }

    public function getBudgetCost($project_id)
    {
        $breakdowns = Breakdown::with('resources')
            ->where('project_id', $project_id)
            ->where('std_activity_id', $this->id)->get();

        $cost = 0;
        foreach ($breakdowns as $b) {
            foreach ($b->resources as $resource) {
                $cost += $resource->budget_cost;
            }
        }
        return $cost;
    }

    function variables()
    {
        return $this->hasMany(StdActivityVariable::class);
    }

    function syncVariables($variables)
    {
        $this->variables()->delete();

        if ($variables) {
            foreach ($variables as $index => $var) {
                $this->variables()->create([
                    'label' => $var,
                    'display_order' => $index + 1
                ]);
            }
        }
    }

    function getVarsAttribute()
    {
        $variables = [];
        foreach ($this->variables as $var) {
            $variables[] = [
                'name' => '$v' . $var->display_order,
                'label' => $var->label,
                'id' => $var->display_order
            ];
        }

        return collect($variables);
    }
}