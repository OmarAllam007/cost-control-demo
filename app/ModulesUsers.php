<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class ModulesUsers extends Model
{
    use HasChangeLog, RecordsUser;
}
