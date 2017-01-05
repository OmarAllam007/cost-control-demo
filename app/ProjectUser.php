<?php

namespace App;

use App\Behaviors\CachesQueries;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use CachesQueries;
}
