<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasChangeLog;
    use CachesQueries, RecordsUser;

    const GENERATING = -1;

    const GENERATED = 1;

    const NONE = 0;

    protected $fillable = [
        'name', 'start_date', 'is_open', 'status', 'global_period_id',
        'planned_cost', 'earned_value', 'actual_invoice_amount', 'planned_progress', 'planned_finish_date',
        'spi_index', 'actual_progress', 'change_order_amount',
        'time_extension', 'time_elapsed', 'time_remaining', 'expected_duration', 'duration_variance',
    ];

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
//        return $query->where('is_open', false)->orderBy('id', 'desc');
        return $query->where('status', self::GENERATED)->orderBy('id', 'desc');
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

    function getChangeOrderAmountAttribute()
    {
        if ($this->attributes['change_order_amount']) {
            return $this->attributes['change_order_amount'];
        }

        return $this->project->change_order_amount;
    }

    function getPlannedFinishDateAttribute()
    {
        if ($this->attributes['planned_finish_date']) {
            return Carbon::parse($this->attributes['planned_finish_date']);
        }

        return Carbon::parse($this->project->expected_finished_date);
    }

    function getContractValueAttribute()
    {
        return $this->change_order_amount + $this->project->project_contract_signed_value;
    }

    public function scopeLast(Builder $query)
    {
        return $query->readyForReporting()->select('project_id')->selectRaw('max(id) as period_id')->groupBy('project_id');
    }
}
