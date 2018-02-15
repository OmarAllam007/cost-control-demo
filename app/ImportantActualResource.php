<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportantActualResource extends Model
{
    protected $fillable = [
        'project_id', 'wbs_level_id', 'breakdown_resource_id', 'period_id', 'original_code',
        'qty', 'unit_price', 'cost', 'unit_id', 'action_date', 'resource_id', 'batch_id', 'doc_no', 'original_data'
    ];

    protected $casts = ['original_data' => 'array'];

    protected $dates = ['created_at', 'update_at', 'action_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_level_id');
    }

    function breakdown_resource()
    {
        return $this->belongsTo(BreakdownResource::class, 'breakdown_resource_id');
    }

    function resource_shadow()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id');
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    function budget()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }
}
