<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 12/19/16
 * Time: 3:45 PM
 */

namespace App\Jobs;


use App\ActualResources;
use App\CostResource;
use App\Project;
use Illuminate\Support\Collection;

class UpdateResourceDictJob
{
    /**
     * @var Project
     */
    private $project;
    /**
     * @var Collection
     */
    private $resource_dict;

    /**
     * UpdateResourceDictJob constructor.
     *
     * @param Project $project
     * @param Collection $resource_dict
     */
    public function __construct($project, $resource_dict)
    {
        $this->project = $project;
        $this->resource_dict = $resource_dict;
    }

    function handle()
    {
        ActualResources::groupBy('project_id', 'resource_id')
            ->select('project_id', 'resource_id')
            ->selectRaw('avg(unit_price) as rate')->selectRaw('max(period_id) as period_id')
            ->where('project_id', $this->project->id)
            ->whereIn('resource_id', $this->resource_dict)
            ->get()->map(function($actualResource) {
                $attrs = $actualResource->toArray();
                $rate = $attrs['rate'];
                unset($attrs['rate']);
                CostResource::firstOrCreate($attrs)->update(compact('rate'));
            });
    }
}