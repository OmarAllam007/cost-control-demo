<?php

namespace App\Http\Controllers\Api;

use App\Project;
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
    function Resources(Project $project)
    {
        return Resources::with('units')->with('types')->where('project_id', $project->id)->get()->map(function (Resources $resource){
            return $resource->morphToJSON();
        });

    }
}
