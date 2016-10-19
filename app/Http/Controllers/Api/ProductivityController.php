<?php

namespace App\Http\Controllers\Api;

use App\Productivity;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ProductivityController extends Controller
{
    function index(Request $request)
    {
        return Productivity::filter($request->get('term'))
            ->get()->map(function (Productivity $productivity) {
                return $productivity->morphToJSON();
            });
    }

    function labors_count(Productivity $productivity)
    {
        return ['count' => $productivity->crew_man];
    }
}
