<?php

namespace App\Console\Commands;

use App\Project;
use App\WbsLevel;
use Illuminate\Console\Command;

class WbsSapCode extends Command
{
    protected $signature = 'sap-code:wbs';

    protected $description = 'Generate a SAP code for each WBS';

    protected $skip = [9371, 9373, 9830, 11383, 12748, 13310, 14104, 20706, 20842, 20944, 21589, 24814, 25346];

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
            if (in_array($level->id, $this->skip)) {
                return true;
            }
            $maxCode = $level->parent->children()->max('sap_code');
            $partial = 1;
            if ($maxCode) {
                $partial = collect(explode('.', $maxCode))->last() + 1;
            }

            $level->sap_code = $level->parent->sap_code . '.' . sprintf('%02d', $partial);
        } else {
            if (in_array($level->id, $this->skip)) {
                return true;
            }
            $level->sap_code = $level->project->project_code;
        }
        $level->save();

        $level->children->each(function ($child) {
            $this->addCode($child);
        });
    }
}
