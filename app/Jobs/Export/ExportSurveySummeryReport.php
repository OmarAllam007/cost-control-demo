<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportSurveySummeryReport extends Job
{

    public function __construct()
    {

    }


    public function handle()
    {
        set_time_limit(300);
        $this->project = $project;

        $this->boqs = Boq::where('project_id', $this->project->id)->get()->keyBy('cost_account')->map(function ($boq) {
            return $boq->description;
        });

        $this->survies = Survey::where('project_id', $this->project->id)->get()->keyBy('cost_account')->map(function ($survey) {
            return $survey->unit->type;
        });

        $wbs_levels = WbsLevel::where('project_id', $project->id)->tree()->get();
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildTree($level->root);
            $tree[] = $treeLevel;
        }
    }
}
