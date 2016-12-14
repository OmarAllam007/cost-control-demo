<?php

namespace App;

use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;


class BreakDownResourceShadow extends Model
{
    use Tree ;
    protected $table = 'break_down_resource_shadows';
    protected $fillable = [
        'breakdown_resource_id',
        'code',
        'project_id',
        'wbs_id',
        'breakdown_id',
        'resource_id',
        'template',
        'activity',
        'activity_id',
        'cost_account',
        'eng_qty',
        'budget_qty',
        'resource_qty',
        'resource_waste',
        'resource_type',
        'resource_type_id',
        'resource_code',
        'resource_name',
        'unit_price',
        'measure_unit',
        'budget_unit',
        'budget_cost',
        'boq_equivilant_rate',
        'labors_count',
        'productivity_output',
        'productivity_ref',
        'remarks',
        'productivity_id',
        'template_id',
        'unit_id',
    ];

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class);
    }

    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'activity_id');
    }

    function breakdown_resource()
    {
        return $this->belongsTo(BreakdownResource::class);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    function breakdown(){
        return $this->belongsTo(Breakdown::class);
    }



}
