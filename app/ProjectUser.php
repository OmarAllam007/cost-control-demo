<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use CachesQueries;
    use HasChangeLog, RecordsUser;

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
