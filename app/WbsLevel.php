<?php

namespace App;

use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbsLevel extends Model
{
    use SoftDeletes;
    use Tree;

    protected $fillable = ['name', 'project_id', 'parent_id', 'comments', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return self::pluck('name', 'id')->prepend('Select Level', '');
    }

    public static function getCode($string)
    {
        $fullstring = explode(" ", $string);
        $code = '';
        for ($i = 0; $i < count($fullstring); $i++) {
            if (substr($fullstring[ $i ], 0, 1) == '0') {
                continue;
            } else {
                $code = $code . substr($fullstring[ $i ], 0, 1) . '';
            }
        }
        return preg_replace("/[^A-Za-z0-9 ]/", '', $code);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForProject(Builder $query, $project_id)
    {
        $query->where('project_id', $project_id);
    }
}