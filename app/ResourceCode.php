<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class ResourceCode extends Model
{
    protected $fillable = ['code', 'project_id', 'resource_id'];
    use HasChangeLog;

    function resource()
    {
        return $this->belongsTo(Resources::class, 'resource_id');
    }
}
