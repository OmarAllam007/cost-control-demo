<?php

namespace App\Http\Controllers\Api;

use App\BreakdownTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class StdActivityResourceController extends Controller
{
    function index()
    {
        $templateId = request('template');
        $template = BreakdownTemplate::find($templateId);

        if (!$template) {
            return [];
        }

        return $template->resources()->recursive()->get()->map(function($resource) {
            return $resource->morphForJSON();
        });
    }
}
