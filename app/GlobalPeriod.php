<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalPeriod extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'spi'];
    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];
    protected $has_project_periods = null;

    function hasProjectPeriods()
    {
        if ($this->has_project_periods !== null) {
            return $this->has_project_periods;
        }

        return $this->has_project_periods = Period::where('global_period_id', $this->id)->exists();
    }
}
