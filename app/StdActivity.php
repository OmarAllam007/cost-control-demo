<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;

class StdActivity extends Model
{
    use HasOptions;
    protected $orderBy = ['name','code'];
    protected static $alias = 'Activity';

    protected $fillable = ['code', 'name', 'division_id', 'word_package_name'];

    protected $dates = ['created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(ActivityDivision::class, 'division_id');
    }

    public function breakdowns()
    {
        return $this->hasMany(BreakdownTemplate::class);
    }
}