<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CostControlController extends Controller
{
    function index()
    {
        $projectGroups = Project::orderBy('client_name')->when(!auth()->user()->is_admin, function($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->where('cost_control', 1)->pluck('project_id');
            $q->whereIn('id', $projects)->orWhere(function ($q) {

            });
        })->get()->groupBy('client_name');

        return view('home.budget', compact('projectGroups'));
    }
}
