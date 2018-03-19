<?php

namespace App\Http\Controllers;

use App\ChangeLog;
use App\Project;
use App\ProjectUser;
use App\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Database\Eloquent\Collection;
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

        $user_id = request('user');

        $logs = ChangeLog::forProjectOnDate($project, $date, $user_id)->paginate(25);

        $project_users = $project->getUsers();

        return view('changelog.show', compact('project', 'logs', 'date', 'project_users', 'user_id'));
    }
}
