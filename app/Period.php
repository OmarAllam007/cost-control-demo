<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['name', 'start_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
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
