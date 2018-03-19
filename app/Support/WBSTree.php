<?php

namespace App\Support;


use App\Project;
use Illuminate\Support\Collection;

class WBSTree
{
    /** @var Project */
    private $project;

    /** @var Collection */
    private $wbs_levels;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function get()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');
        return $this->buildTree();
    }

    private function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            return $level;
        });
    }
}