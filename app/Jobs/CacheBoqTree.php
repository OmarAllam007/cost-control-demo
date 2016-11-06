<?php

namespace App\Jobs;


use App\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class CacheBoqTree extends Job
{
    use InteractsWithQueue, SerializesModels;

    public $project;


    function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function handle()
    {
        $boqitems = $this->buildBoqArray($this->project);
        return $boqitems;
    }

    private function buildBoqArray($project)
    {
        $items = [];
        foreach ($project->boqs as $boq) {
            if (!isset($items[ $boq->type ])) {
                $items[ $boq->type ] = [
                    'name' => $boq->type,
                    'items' => [],
                ];
            }
            $items[ $boq->type ]['items'][] = $boq;
        }

        return $items;
    }

}
