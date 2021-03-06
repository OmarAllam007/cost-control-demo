<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetChangeRequest extends Model
{
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'closed_at'];

    protected $casts = ['closed' => 'boolean', 'qty' => 'float', 'unit_price' => 'float'];
    static $statuses = [0 => 'Select Status', 1 => 'Open', 2 => 'Closed'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class);
    }

    function activity()
    {
        return $this->belongsTo(StdActivity::class);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function closed_by()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }


    function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

}
