<?php

namespace App;

use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class CostIssue extends Model
{
//    use RecordsUser;

    protected $fillable = ['batch_id', 'type', 'data'];

    function batch()
    {
        return $this->belongsTo(ActualBatch::class, 'batch_id');
    }
}
