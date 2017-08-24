<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/11/2016
 * Time: 2:53 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;

class BoqPriceList
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    private $boqs;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }


    private $survies;
    private $shadows;
    private $boq_equavalent_rate;

    public function run()
    {
        $this->boqs = Boq::where('project_id', $this->project->id)->get()->groupBy('wbs_id');

        $this->wbs_levels = WbsLevel::where('project_id', $this->project->id)->with('boqs', 'boqs.unit')->get();

        $wbs_levels = $this->wbs_levels->where('parent_id', 0)->map([$this, 'getReportTree']);

        $raw_boqs = \DB::select('select boq_id, resource_type, sum(boq_equivilant_rate) as rate from break_down_resource_shadows where project_id = ? group by boq_id, resource_type', [$this->project->id]);

        $boqs = collect($raw_boqs)->groupBy('boq_id')->map(function (Collection $group) {
                return $group->groupBy('resource_type');
            });

        return ['wbs_levels' => $wbs_levels, 'project' => $this->project, 'boqs' => $boqs];
    }

    public function getReportTree(WbsLevel $level)
    {
        $level->children = $this->wbs_levels->where('parent_id', $level->id);

        return $level;
    }


}

