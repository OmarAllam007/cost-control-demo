<?php

namespace App\Console\Commands;

use App\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Message;

class CostUpdateReminder extends Command
{
    protected $signature = 'cost:reminder';

    protected $description = 'Send a reminder to upload cost to projects';

    /** @var Carbon */
    protected $today;

    public function handle()
    {
        $projects = Project::all();

        $startDate = Carbon::parse('-7 days');
        $endDate = Carbon::yesterday();

        foreach ($projects as $project) {
            $period = $project->open_period();
            if (!$period) {
                continue;
            }

            /** @var Collection $users */
            $users = $project->users()->wherePivot('actual_resources', true)->get();
            if ($project->cost_owner) {
                $users->add($project->cost_owner);
            }

            foreach ($users as $user) {
                \Mail::send('mail.cost-reminder', compact('project', 'period', 'startDate', 'endDate', 'user'), function(Message $message) use ($user, $project){
                    $message->to($user->email);
                    $message->subject('[KPS] Reminder for ' . $project->name);
                });
            }
        }

    }
}
