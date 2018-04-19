<?php

namespace App\Console\Commands;

use App\Project;
use App\WbsLevel;
use Illuminate\Console\Command;

class WbsSapCode extends Command
{
    protected $signature = 'sap-code:wbs';

    protected $description = 'Generate a SAP code for each WBS';

    public function __construct()
    {
        parent::__construct();
        WbsLevel::flushEventListeners();
    }

    public function handle()
    {
        Project::each(function (Project $project) {
            $project->wbs_levels()->parents()->each(function($level) {
                $this->addCode($level);
            });
        });
    }

    function addCode(WbsLevel $level) {
        if ($level->parent) {
            $maxCode = $level->parent->children()->max('sap_code');
            $partial = 1;
            if ($maxCode) {
                $partial = collect(explode('.', $maxCode))->last() + 1;
            }

            $level->sap_code = $level->parent->sap_code . '.' . sprintf('%02d', $partial);
        } else {
            $level->sap_code = $level->project->project_code;
        }
        $level->save();

        $level->children->each(function ($child) {
            $this->addCode($child);
        });
    }
}
