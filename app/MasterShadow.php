<?php

namespace App;

use App\Behaviors\ChartScopes;
use App\Behaviors\ReportScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MasterShadow extends Model
{
    protected $casts = [
        'wbs' => 'array',
        'activity_divs' => 'array',
        'resource_divs' => 'array',
    ];

    function wbs_level()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    function boq_record()
    {
        return $this->belongsTo(Boq::class, 'boq_id');
    }

    function boq_wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'boq_wbs_id');
    }

    public function scopeForPeriod(Builder $query, Period $period)
    {
        return $query->wherePeriodId($period->id)->where('master_shadows.project_id', $period->project_id);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    use ChartScopes, ReportScopes;
}
