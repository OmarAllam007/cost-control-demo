<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbsLevel extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'project_id', 'parent_id', 'comments'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return self::pluck('name', 'id')->prepend('Select Level', '');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeTree(Builder $query)
    {
        $query->parents()
            ->with('children')
            ->with('children.children')
            ->with('children.children.children');
    }

    public function scopeParents(Builder $query)
    {
        $query->where('parent_id', 0);
    }
}