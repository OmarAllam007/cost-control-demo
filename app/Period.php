<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasChangeLog;
    use CachesQueries;

    protected $fillable = ['name', 'start_date', 'is_open'];

    protected $dates = ['created_at', 'update_at', 'start_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function batches()
    {
        $relation = $this->hasMany(ActualBatch::class);
        $relation->orderBy('id', 'DESC');
        return $relation;
    }

    function scopeReadyForReporting(Builder $query)
    {
        return $query->where('is_open', false)->orderBy('id', 'desc');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function(Period $period) {
            $open = $period->project->active_period;
            if (!$open) {
                $period->is_open= true;
            }
        });

        static::saving(function ($period) {
            if ($period->is_open) {
                static::where('project_id', $period->project_id)->update(['is_open' => false]);
            }
        });
    }
}
