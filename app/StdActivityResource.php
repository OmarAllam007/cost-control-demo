<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StdActivityResource extends Model
{
    use SoftDeletes, CachesQueries;
    use HasChangeLog;

    protected $fillable = ['template_id', 'resource_id', 'equation', 'budget_qty', 'eng_qty', 'allow_override', 'project_id', 'labor_count', 'productivity_id', 'remarks', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public $old_equation;

    public function template()
    {
        return $this->belongsTo(BreakdownTemplate::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resources::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function std_activity()
    {
        return $this->hasMany(StdActivity::class);
    }

    function scopeRecursive(Builder $query)
    {
        $query->with('resource')
            ->with('resource.units')
            ->with('resource.types');
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class);
    }

//    function variables()
//    {
//        return $this->hasMany(StdActivityVariable::class);
//    }


    function morphForJSON($account, $request)
    {
        $attributes = [
            'std_activity_resource_id' => $this->id,
            'equation' => $this->equation,
            'labor_count' => $this->labor_count,
            'productivity_id' => $this->productivity_id,
            'productivity_ref' => $this->productivity ? $this->productivity->csi_code : '',
            'resource_id' => $this->resource->id,
            'resource_name' => $this->resource->name,
            'resource_waste' => $this->resource->waste,
            'unit' => isset($this->resource->units->type) ? $this->resource->units->type : '',
            'resource_type' => $this->resource->types->root->name ?? '',
            'budget_qty' => '',
            'eng_qty' => '',
            'remarks' => $this->remarks,
            'variables' => $this->template->activity->variables()->pluck('label', 'display_order'),
        ];
        /** @var WbsLevel $wbs_level */
        $wbs_level = WbsLevel::find($request['wbs_level_id']);
        $eng_qty = $wbs_level->getEngQty($request['cost_account']);
        $budget_qty = $wbs_level->getBudgetQty($request['cost_account']);

        $costAccount = Survey::where('cost_account', $account)->first();
        if ($costAccount) {
            $attributes['eng_qty'] = $eng_qty;
            $attributes['budget_qty'] = $budget_qty;
        }

        return $attributes;
    }

    function updateShadows()
    {
        if (isset($this->template->project_id)) {
            $breakdown_resources = BreakdownResource::whereHas('breakdown',function ($q){
                $q->where('project_id',$this->template->project_id);
            })->where('std_activity_resource_id', $this->id)->get();

            foreach ($breakdown_resources as $breakdown_resource) {
                if (!$breakdown_resource->equation || $breakdown_resource->equation == $this->old_equation) {
                    $breakdown_resource->equation = $this->equation;
                }
                $breakdown_resource->remarks = $this->remarks;
                $breakdown_resource->productivity_id = $this->productivity ? $this->productivity_id : '';
                $breakdown_resource->update();
//                $formatter = new BreakdownResourceFormatter($breakdown_resource);
//                BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)
//                    ->update($formatter->toArray());
            }
        }


    }

}