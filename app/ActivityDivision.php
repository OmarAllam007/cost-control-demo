<?php

namespace App;

use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityDivision extends Model
{
    use Tree;
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'parent_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function getLabelAttribute()
    {
        return $this->code . '.' . $this->name;
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }
}