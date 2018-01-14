<?php

namespace App\Http\Controllers;

use App\ChangeLog;
use App\Project;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Http\Request;

class ChangelogController extends Controller
{
    function show(Project $project, Request $request)
    {
        try {
            $date = Carbon::parse($request->get('date'));
        } catch (InvalidDateException $e) {
            $date = Carbon::now();
        }

        $logs = ChangeLog::forProjectOnDate($project, $date)->paginate(25);

        return view('changelog.show', compact('project', 'logs', 'date'));
    }
}
