<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectUser;
use Illuminate\Http\Request;

use App\Http\Requests;

class BudgetController extends Controller
{
    function index()
    {
        $projectGroups = Project::orderBy('client_name')->when(!auth()->user()->is_admin, function($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->pluck('project_id');
            $q->whereIn('id', $projects)->orWhere('owner_id', auth()->id());
        })->get()->groupBy('client_name');

        return view('home.budget', compact('projectGroups'));
    }
}
