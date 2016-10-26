<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\StdActivity;

class StdActivityController extends Controller
{
    function variables(StdActivity $std_activity)
    {
        return $std_activity->variables ?: [];
    }
}