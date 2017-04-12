<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use CachesQueries;
    use HasChangeLog;

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
