<?php

namespace App\Sap;

use App\BreakDownResourceShadow;
use App\StdActivity;
use App\Support\WBSTree;
use Illuminate\Support\Collection;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

class ExportBudget
{
    private $project;
    /** @var PHPExcel_Worksheet */
    private $sheet;

    /** @var int */
    private $counter = 2;

    /** @var Collection */
    private $tree;

    /** @var Collection */
    private $wbs_path;

    /** @var Collection */
    private $division_cache;

    public function __construct($project)
    {
        $this->project = $project;
        $this->wbs_path = collect();
        $this->division_cache = collect();
    }

    function handle()
    {
        $excel = new PHPExcel();
        $this->sheet = $excel->getSheet(0);

        $this->sheet->fromArray([
            "WBS Code", "KPS Code", "SAP Code", "WBS Level 1", "WBS Level 2", "WBS Level 3", "WBS Level 4",
            "WBS Level 5", "WBS Level 6", "WBS Level 7", "WBS Level", "Activity Division", "Activity", "Budget Cost"
        ], null, "A1", true);

        $this->tree = (new WBSTree($this->project))->get();

        $this->buildWbs($this->tree,0);

        for ($c = "A"; $c < "O"; ++$c) {
            $this->sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $this->sheet->getStyle("A1:N1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'EFF8FF']],
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => '3490DC']]
        ]);

        $filename = storage_path('app/' . uniqid('project_budget_for_sap_') . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return $filename;
    }

    private function buildWbs($tree, $depth = 0)
    {
        $tree->each(function($wbs) use ($depth) {
            while ($this->wbs_path->count() > $depth) {
                $this->wbs_path->pop();
            }

            $this->wbs_path->push($wbs->name);

            $this->sheet->fromArray([$wbs->code, $wbs->code, $wbs->sap_code], null, "A{$this->counter}");
            $this->sheet->fromArray($this->wbs_path->slice(0, 7)->toArray(), null, "D{$this->counter}");
            $this->sheet->setCellValue("K{$this->counter}", $wbs->name);

            $budget_cost = BreakDownResourceShadow::whereIn('wbs_id', $wbs->getChildrenIds())->sum('budget_cost');
            $this->sheet->setCellValue("N{$this->counter}", round($budget_cost, 2));

            ++$this->counter;

            $this->addActivities($wbs);

            $this->buildWbs($wbs->subtree, $depth + 1);
        });
    }

    private function addActivities($wbs)
    {
        $shadows = $this->project->shadows()->where('wbs_id', $wbs->id)->with('std_activity')
            ->selectRaw('activity_id, activity, code, sap_code, sum(budget_cost) as budget_cost')
            ->groupBy(['activity_id', 'activity', 'code', 'sap_code'])->get();

        $divisions = $shadows->map(function($shadow) {
            return $shadow->std_activity->division->root;
        })->unique()->each(function($division) use ($wbs) {
            $activities = StdActivity::whereIn('division_id', $division->getChildrenIds())->pluck('id');
            $shadows = $this->project->shadows()->where('wbs_id', $wbs->id)->with('std_activity')
                ->whereIn('activity_id', $activities)
                ->selectRaw('activity_id, activity, code, sap_code, sum(budget_cost) as budget_cost')
                ->groupBy(['activity_id', 'activity', 'code', 'sap_code'])->get();
            $budget_cost = $shadows->sum('budget_cost');

            $code = '.' . str_replace('.', '', $division->code);
            $this->sheet->fromArray([$wbs->code, $wbs->code . $code, $wbs->sap_code . $code], null, "A{$this->counter}");
            $this->sheet->fromArray($this->wbs_path->slice(0, 7)->toArray(), null, "D{$this->counter}");
            $this->sheet->setCellValue("K{$this->counter}", $division->name);
            $this->sheet->setCellValue("L{$this->counter}", $division->name);
            $this->sheet->setCellValue("N{$this->counter}", $budget_cost);
            ++$this->counter;

            foreach ($shadows as $shadow) {
                $this->sheet->fromArray([$wbs->code, $shadow->code, $shadow->sap_code], null, "A{$this->counter}");
                $this->sheet->fromArray($this->wbs_path->slice(0, 7)->toArray(), null, "D{$this->counter}");
                $this->sheet->fromArray([
                    $shadow->activity, $division->name, $shadow->activity, round($shadow->budget_cost, 2)
                ], null, "K{$this->counter}");

                ++$this->counter;
            }
        });



        foreach ($shadows as $shadow) {

            if (!$this->division_cache->has($shadow->std_activity->division_id)) {
                $this->division_cache->put($shadow->std_activity->division_id, $shadow->std_activity->division->root);
            }

            $division = $this->division_cache->get($shadow->std_activity->division_id);


        }
    }
}