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
    protected $open_period_id;
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
    public function __construct($project, $resource_dict = null)
    {
        $this->project = $project;
        $this->open_period_id = $this->project->open_period()->id;

        if ($resource_dict) {
            $this->resource_dict = $resource_dict;
        } else {
            $this->resource_dict = collect();
        }
    }

    function handle()
    {
        $query = ActualResources::groupBy('project_id', 'resource_id', 'period_id')
            ->select('project_id', 'resource_id')
            ->selectRaw('avg(unit_price) as rate')
            ->where('project_id', $this->project->id)
            ->where('period_id', $this->open_period_id);

        if ($this->resource_dict->count()) {
            $query->whereIn('resource_id', $this->resource_dict);
        }

        $query->get()->map(function ($actualResource) {
            $attrs = $actualResource->toArray();
            $attrs['period_id'] = $this->open_period_id;
            $rate = $attrs['rate'];
            unset($attrs['rate']);
            CostResource::firstOrCreate($attrs)->update(compact('rate'));
        });
    }
}