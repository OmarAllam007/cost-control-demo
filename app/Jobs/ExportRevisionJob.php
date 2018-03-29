<?php

namespace App\Jobs;

use App\BudgetRevision;
use App\Project;
use App\Revision\RevisionBoq;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Database\Eloquent\Collection;

class ExportRevisionJob extends Job
{
    protected $counter;

    /** @var  \Illuminate\Support\Collection */
    protected $divisionsCache;

    /** @var  \Illuminate\Support\Collection */
    protected $wbsCache;

    /** @var BudgetRevision */
    private $revision;

    /** @var Project */
    protected $project;

    /** @var \PHPExcel_Worksheet */
    protected $sheet;

    public function __construct(BudgetRevision $revision)
    {
        $this->revision = $revision;
        $this->project = $revision->project;

        $this->divisionsCache = collect();
        $this->wbsCache = collect();
    }

    public function handle()
    {
        \PHPExcel_Settings::setCacheStorageMethod(\PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp, array( 'memoryCacheSize' => '128MB'));

        $excel = new \PHPExcel;
        $this->sheet = $excel->getSheet(0);
        $this->counter = 2;

        $headers = [
            'WBS-Level-1', 'WBS-Level-2', 'WBS-Level-3', 'WBS-Level-4', 'WBS-Level-5', 'WBS-Level-6', 'WBS-Level-7', 'WBS Path',
            'Activity-Division-1', 'Activity-Division-2', 'Activity-Division-3', 'Activity-Division-4', 'Activity ID', 'Activity', 'Discipline',
            'Breakdown-Template', 'Cost Account', 'BOQ item Description',
            'Engineering Quantity', 'Budget Quantity',
            'Resource Quantity', 'Resource Waste', 'Resource Type', 'Resource Code', 'Resource Name', 'Price - Unit', 'Unit Of Measure', 'Budget Unit', 'Budget Cost',
            'BOQ Equivalent Unit Rate', 'No. Of Labors', 'Productivity (Unit/Day)', 'Productivity Reference', 'Remarks'
        ];
        $this->sheet->fromArray($headers, '', 'A1');

        RevisionBreakdownResourceShadow::where(['project_id' => $this->project->id, 'revision_id' => $this->revision->id])
            ->chunk(500, function (Collection $collection) {
                $collection->each(function (RevisionBreakdownResourceShadow $resource) {
                    $discpline = $resource->std_activity->discipline;
                    $level = $resource->wbs;
                    if (!$level) {
                        return true;
                    }

                    $levels = $this->resolveWbsLevels($level);
                    $divisions = $this->resolveRevisions($resource);
                    $boq = RevisionBoq::find($resource->boq_id);

                    $row = [
                        $levels[0] ?? '',
                        $levels[1] ?? '',
                        $levels[2] ?? '',
                        $levels[3] ?? '',
                        $levels[4] ?? '',
                        $levels[5] ?? '',
                        $levels[6] ?? '',
                        $level->code,
                        $divisions[0] ?? '',
                        $divisions[1] ?? '',
                        $divisions[2] ?? '',
                        $divisions[3] ?? '',
                        $resource['code'],
                        $resource['activity'],
                        $discpline,
                        $resource['template'],
                        $resource['cost_account'],
                        $boq ? $boq->description : '',
                        $resource['eng_qty'] ?: 0,
                        $resource['budget_qty'] ?: 0,
                        $resource['resource_qty'] ?: 0,
                        $resource['resource_waste'] ?: 0,
                        $resource['resource_type'],
                        $resource['resource_code'],
                        $resource['resource_name'],
                        $resource['unit_price'] ?: 0,
                        $resource['measure_unit'],
                        $resource['budget_unit'] ?: 0,
                        $resource['budget_cost'] ?: 0,
                        $resource['boq_equivilant_rate'] ?: 0,
                        $resource['labors_count'] ?: 0,
                        $resource['productivity_output'] ?: 0,
                        $resource['productivity_ref'],
                        $resource['remarks'],
                    ];

                    $this->sheet->fromArray($row, '', "A{$this->counter}");
                    ++$this->counter;
                });

                \Log::info("Chunk " . intval($this->counter / 1000) . ' added; memory: ' . memory_get_usage(true));
            });

        $filename = storage_path('app/' . slug($this->project->name) . '_' . slug($this->revision->name) . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->save($filename);
        return $filename;
    }

    protected function resolveRevisions(RevisionBreakdownResourceShadow $resource)
    {
        $id = $resource->std_activity->id;
        if ($this->divisionsCache->has($id)) {
            return $this->divisionsCache->get($resource->std_activity->id);
        }

        $division = $resource->std_activity->division;
        $divisions = [];
        $divisions[] = $division->name;
        $parentDiv = $division->parent;
        while ($parentDiv) {
            $divisions[] = $parentDiv->name;
            $parentDiv = $parentDiv->parent;
        }
        $divisions = array_reverse($divisions);
        $this->divisionsCache->put($id, $divisions);
        return $divisions;
    }

    protected function resolveWbsLevels($level)
    {
        if ($this->wbsCache->has($level->id)) {
            return $this->wbsCache->get($level->id);
        }

        $levels = [];
        $levels[] = $level->name;

        $parent = $level->parent;
        while ($parent) {
            $levels[] = $parent->name;
            $parent = $parent->parent;
        };
        $levels = array_reverse($levels);
        $this->wbsCache->put($level->id, $levels);
        return $levels;
    }
}
