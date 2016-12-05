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
        'project_id',
        'wbs_id',
        'breakdown_id',
        'activity_id',
        'resource_type_id',
        'template',
        'activity',
        'cost_account',
        'eng_qty',
        'budget_qty',
        'resource_qty',
        'resource_waste',
        'resource_type',
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
