<?php

namespace App\Http\Controllers\Api;

use App\Resources;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ResourcesController extends Controller
{
    function index(Request $request)
    {
        return Resources::filter($request->get('term'))
            ->get()->map(function (Resources $resource) {
                return $resource->morphToJSON();
            });
    }
}
