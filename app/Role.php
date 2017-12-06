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
        return $this->belongsToMany(Report::class);
    }

    public function addReports($reports)
    {
        $result = collect();
        foreach ($reports as $report_id) {
            $result->push($this->reports()->create(compact('report_id')));
        }

        return $result;
    }

    public function updateReports($reports)
    {
        $result = collect();

        foreach ($reports as $report_id) {
            $result->push($this->reports()->firstOrCreate(compact('report_id')));
        }

        $this->reports()->whereNotIn('id', $result->pluck('id'))->delete();

        return $result;
    }

    protected static function boot()
    {
        parent::boot();

        self::observe(RoleObserver::class);
    }
}
