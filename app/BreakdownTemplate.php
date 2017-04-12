<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownTemplate extends Model
{
    use SoftDeletes, HasOptions, CachesQueries;
    use HasChangeLog;

    protected static $alias = 'Template';

    protected $fillable = ['name', 'code', 'std_activity_id', 'project_id', 'parent_template_id'];

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

    public function scopePublic(Builder $query)
    {
        return $query->where(function (Builder $q){
            $q->whereNull('project_id')->orWhere('project_id', 0);
        });
    }

    public function morphToJSON(){
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'parent_template_id' => $this->parent_template_id,
            'std_activity_id'=>$this->std_activity_id,
            'project_id'=>$this->project_id,
        ];
    }

}