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

    private $chached_eac_profit;

    protected $fillable = [
        'start_date', 'is_open', 'status', 'global_period_id',
        'planned_cost', 'earned_value', 'actual_invoice_amount', 'planned_progress', 'planned_finish_date',
        'spi_index', 'actual_progress', 'change_order_amount', 'forecast_finish_date',
        'time_extension', 'time_elapsed', 'time_remaining', 'expected_duration', 'duration_variance',
        'planned_value', 'actual_invoice_value',
    ];

    protected $dates = ['created_at', 'update_at', 'start_date'];

    function global_period()
    {
        return $this->belongsTo(GlobalPeriod::class);
    }

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

            $period->name = $period->global_period->name;
        });
    }

    function getPlannedFinishDateAttribute()
    {
        if ($this->attributes['planned_finish_date']) {
            return Carbon::parse($this->attributes['planned_finish_date']);
        }

        return Carbon::parse($this->project->original_finish_date);
    }

    function getContractValueAttribute()
    {
        return $this->change_order_amount + $this->project->project_contract_signed_value;
    }

    function getExpectedDurationAttribute()
    {
        if ($this->attributes['expected_duration']) {
            return $this->attributes['expected_duration'];
        }

        return $this->project->project_duration ?: 0;
    }

    public function scopeLast(Builder $query)
    {
        return $query->readyForReporting()->select('project_id')->selectRaw('max(id) as period_id')->groupBy('project_id');
    }

    function getStartDateAttribute()
    {
        if ($this->global_period) {
            return $this->global_period->start_date;
        }

        return Carbon::parse($this->attributes['start_date']);
    }

    function getEndDateAttribute()
    {
        if ($this->global_period) {
            return $this->global_period->end_date;
        }

        return Carbon::parse($this->attributes['end_date']);
    }

    function getAtCompletionCostAttribute()
    {
        return MasterShadow::where('period_id', $this->id)->sum('completion_cost');
    }

    function getEacProfitAttribute()
    {
        if (!empty($this->chached_eac_profit)) {
            return  $this->chached_eac_profit;
        }

        return $this->chached_eac_profit = $this->project->eac_contract_amount - $this->at_completion_cost;
    }

    function getEacProfitabilityIndexAttribute()
    {
        return $this->eac_profit * 100 / $this->project->eac_contract_amount;
    }


}
