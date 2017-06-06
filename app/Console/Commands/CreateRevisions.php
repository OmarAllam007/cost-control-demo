<?php

namespace App\Console\Commands;

use App\BudgetRevision;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateRevisions extends Command
{
    protected $signature = 'revisions:create';

    protected $description = 'Create revisions';

    public function handle()
    {
        $today = Carbon::today();

        BudgetRevision::groupBy('project_id')
            ->selectRaw('project_id, min(id) as first_rev_id')
            ->get()->each(function($rev) use ($today) {
                $firstRevision = BudgetRevision::find($rev->first_rev_id);
                $project = $firstRevision->project;
                $lastRevision = $project->revisions()->where('id', '>', $firstRevision->id)
                    ->whereIsAutomatic(1)->latest()->first();

                if (!$lastRevision) {
                    $lastRevision = $firstRevision;
                }

                /** @var Carbon $date */
                $date = $lastRevision->created_at->addMonths(3);

                if ($date->gt($today)) {
                    return true;
                }

                $max = $project->revisions()->max('rev_num');
                $max++;

                $revision = new BudgetRevision(['name' => 'Rev_' . sprintf("%02d", $max)]);

                $revision->project_id = $project->id;
                $revision->is_automatic = 1;

                $revision->save();

                $this->output->block("Revision {$revision->name} has been created for project {$project->name}");
            });
    }
}
