<?php

namespace App\Console\Commands;

use App\BudgetRevision;
use Illuminate\Console\Command;

class CreateRevisions extends Command
{
    protected $signature = 'revisions:create';

    protected $description = 'Create revisions';

    public function handle()
    {
        BudgetRevision::groupBy('project_id')
            ->selectRaw('project_id, min(id) as first_rev_id')
            ->get()->each(function($rev) {
                dd($rev);
            });


    }
}
