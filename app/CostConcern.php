<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class CostConcern extends Model
{
    protected $table = 'cost_concerns';
    protected $fillable = ['report_name', 'data', 'project_id', 'period_id', 'comment'];

    protected $casts = [
        'array' => ['data']
    ];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    static function boot()
    {
        self::creating(function ($issue) {
            $issue->user_id = auth()->id() ?: 2;
        });
    }
}
