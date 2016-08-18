<?php

namespace App;

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
}