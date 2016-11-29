<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownTemplate extends Model
{
    protected static $alias = 'Template';

    use SoftDeletes, HasOptions;

    protected $fillable = ['name', 'code', 'std_activity_id', 'project_id', 'wbs_id', 'parent_template_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function activity()
    {
        return $this->belongsTo(StdActivity::class, 'std_activity_id');
    }

    public function resources()
    {
        return $this->hasMany(StdActivityResource::class, 'template_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}