<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualBatch extends Model
{
    protected $fillable = ['user_id', 'type', 'file', 'project_id', 'period_id'];

    function issues()
    {
        return $this->hasMany(CostIssue::class, 'batch_id');
    }
}
