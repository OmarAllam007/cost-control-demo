<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StdActivityResource extends Model
{
    protected $fillable = ['template_id', 'resource_id', 'equation', 'default_value', 'allow_override', 'project_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function template()
    {
        return $this->belongsTo(BreakdownTemplate::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    function scopeRecursive(Builder $query)
    {
        $query->with('resource')
            ->with('resource.units')
            ->with('resource.types');
    }

    function morphForJSON()
    {
        return [
            'equation' => $this->equation,
            'labors_count' => $this->labors_count,
            'productivity_id' => $this->productivity_id,
            'resource_id' => $this->resource->id,
            'resource_name' => $this->resource->name,
            'resource_waste' => $this->resource->waste,
            'unit' => $this->resource->units->type,
            'resource_type' => $this->resource->types->name,
            'budget_quantity' => $this->default_value
        ];
    }
}