<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

class CacheProjects extends Command
{

    protected $signature = 'projects:cache';

    protected $description = 'Cache Projects for display';

    public function handle()
    {
        Cache::put('projects_for_budget', (new \App\Support\BudgetProjects())->run(), 10);
        Cache::put('projects_for_cost_control', (new \App\Support\CostControlProjects())->run(), 10);
    }
}
