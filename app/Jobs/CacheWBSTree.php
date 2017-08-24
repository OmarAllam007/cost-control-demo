<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Project;
use App\WbsLevel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheWBSTree extends Job
{
    /**
     * @var Project
     */
    public $project;


    function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function handle()
    {

        $this->wbs_levels = WbsLevel::where('project_id', $this->project->id)->get();


        return $this->wbs_levels->where('parent_id', 0)->map([$this, 'buildTree'])->values()->toArray();

    }

    public function buildTree(WbsLevel $wbs_level)
    {
        $tree = ['id'=>$wbs_level->id,'name'=>$wbs_level->name,'code'=>$wbs_level->code];

        $tree['children'] = $this->wbs_levels->where('parent_id', $wbs_level->id)->map([$this, 'buildTree'])->values()->toArray();

        return $tree;
    }


}
