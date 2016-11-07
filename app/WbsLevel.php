<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbsLevel extends Model
{
    use SoftDeletes;
    use Tree, HasOptions;

    protected $fillable = ['name', 'project_id', 'parent_id', 'comments', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return self::pluck('name', 'id')->prepend('Select Level', '');
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForProject(Builder $query, $project_id)
    {
        $query->where('project_id', $project_id);
    }

    public function deleteRecursive()
    {
        if ($this->children->count()) {
            $this->children->each(function($level){
                $level->deleteRecursive();
            });
        }

        $this->delete();
    }
}