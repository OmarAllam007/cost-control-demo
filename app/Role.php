<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;
use \App\Observers\RoleObserver;

class Role extends Model
{
    use HasChangeLog, RecordsUser;

    protected $fillable = ['name', 'description'];

    function reports()
    {
        return $this->belongsToMany(Report::class, 'role_reports');
    }

    function budget_reports()
    {
        return $this->reports()->where('type', 'budget');
    }

    function cost_reports()
    {
        return $this->reports()->where('type', 'Cost Control');
    }

    function hasReport($id)
    {
        return $this->reports->pluck('pivot.report_id', 'pivot.report_id')->has($id);
    }

    protected static function boot()
    {
        parent::boot();

        self::observe(RoleObserver::class);
    }
}
