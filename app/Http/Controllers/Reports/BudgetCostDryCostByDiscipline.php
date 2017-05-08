<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:00 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Beta\B;
use Illuminate\Database\Eloquent\Collection;

class BudgetCostDryCostByDiscipline
{
    /**
     * @var Project
     */
    protected $project;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $project = $this->project;

        /** @var Collection $budgetData */
        $budgetData = BreakDownResourceShadow::from('break_down_resource_shadows as sh')
            ->where('sh.project_id', $project->id)
            ->join('std_activities as a', 'sh.activity_id', '=', 'a.id')->selectRaw("CASE WHEN a.discipline != '' THEN a.discipline ELSE 'General' END as type")
            ->selectRaw('sum(budget_cost) as budget_cost')
            ->groupBy(\DB::raw(1))->orderByRaw('1')
            ->get()->keyBy(function($row) {
                return trim(strtolower($row->type));
            });

        $boqData = Boq::whereProjectId($project->id)->groupBy('type')
            ->selectRaw('type, sum(dry_ur * quantity) as dry_cost')->get()->keyBy(function($row) {
                return trim(strtolower($row->type));
            });

        /*$budgetData->keys()->each(function($discipline) use ($boqData, $project) {

            $dry = Boq::whereIn('id', function($query) use ($project, $discipline) {
                // Subquery
                $query->from('break_down_resource_shadows as sh')
                    ->join('std_activities as a', 'sh.activity_id', '=', 'a.id')
                    ->where('sh.project_id', $project->id)
                    ->where('a.discipline', $discipline)
                    ->select('boq_id');
            })->selectRaw('sum(dry_ur * quantity) as dry_cost')->first();

            $boqData->put($discipline, $dry);
        });*/

        return view('reports.budget_cost_dry_cost_by_discipline', compact('budgetData', 'boqData', 'project'));
    }


    public function getBudgetCostDryCostColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('Budget Cost')->addNumberColumn('Dry Cost')->addNumberColumn('Budget Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$key, $data[$key]['dry'], $data[$key]['cost']]);

        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget Cost VS Dry Cost'),
            'height' => 400,
            'hAxis' => [
                'title' => 'Discipline',
            ],
            'vAxis' => [
                'title' => '',
            ],

        ];
        \Lava::ColumnChart('BudgetCost', $costTable, $options);

    }

    public function getBudgetCostDryCostSecondColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('Difference')->addNumberColumn('Dry Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$key, $data[$key]['difference']]);
        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget Cost VS Dry Cost'),
            'height' => 400,
            'hAxis' => [
                'title' => 'Discipline',
            ],
            'vAxis' => [
                'title' => '',
            ],

        ];
        \Lava::ColumnChart('Difference', $costTable, $options);

    }

}