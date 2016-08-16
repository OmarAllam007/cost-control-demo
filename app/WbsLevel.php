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


}