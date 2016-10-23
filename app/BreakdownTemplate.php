<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownTemplate extends Model
{
    protected static $alias = 'Template';

    use SoftDeletes, HasOptions;

    protected $fillable = ['name', 'code', 'std_activity_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function activity()
    {
        return $this->belongsTo(StdActivity::class, 'std_activity_id')->withTrashed();
    }

    public function resources()
    {
        return $this->hasMany(StdActivityResource::class, 'template_id');
    }
}