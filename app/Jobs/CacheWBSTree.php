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
    use InteractsWithQueue, SerializesModels;

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

        $wbs_levels = WbsLevel::where('project_id', $this->project->id)->tree()->get();
        $tree = [];
        foreach ($wbs_levels as $wbs_level) {
            $levelTree = $this->buildTree($wbs_level);
            $tree[] = $levelTree;
        }
        return $tree;
    }

    protected function buildTree(WbsLevel $wbs_level)
    {
        $tree = ['id'=>$wbs_level->id,'name'=>$wbs_level->name,'code'=>$wbs_level->code,'children'=>[]];
        if($wbs_level->children()->count())
        {
            $tree['children'] = $wbs_level->children->map(function(WbsLevel $childLevel){
                return $this->buildTree($childLevel);
            });
        }

        return $tree;
    }


}
