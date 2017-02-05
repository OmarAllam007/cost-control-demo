<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostIssue extends Model
{
    protected $fillable = ['batch_id', 'type', 'data'];

    function batch()
    {
        return $this->belongsTo(ActualBatch::class, 'batch_id');
    }
}
