<?php

namespace App\Jobs;

use App\ActivityMap;
use App\ActualResources;
use App\BreakdownResource;
use App\Period;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\Unit;
use App\UnitAlias;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportActualMaterialJob extends ImportJob
{
    /**
     * @var Collection
     */
    protected $activityMap;

    /**
     * @var Collection
     */
    protected $resourceMap;

    /**
     * @var Collection
     */
    protected $activityCodes;

    /**
     * @var Collection
     */
    protected $resourceIds;

    /**
     * @var Period
     */
    protected $active_period;

    /**
     * @var Collection
     */
    protected $unitsMap;


    /**
     * @var Project
     */
    private $project;

    private $file;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->active_period = $project->open_period;
        $this->file = $file;
        $this->loadActivityMap();
        $this->loadResourceMap();
        $this->loadUnits();
    }


    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $counter = 0;

        $result = [
            'success' => 0,
            'hasIssues' => false,
            'mapping' => [
                'activities' => collect(),
                'resources' => collect(),
            ],
            'units' => collect(),
            'multiple' => collect(),
            'invalid' => collect(),
            'project' => $this->project
        ];

        $excelBaseDate = Carbon::create(1899, 12, 30);

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $activityCode = strtolower($data[3]);
            if (!$this->activityMap->has($activityCode)) {
                $result['mapping']['activities']->push($data);
                continue;
            }
            $activityCodes = $this->activityMap->get($activityCode);

            $resourceCode = strtolower($data[13]);
            if (!$this->resourceMap->has($resourceCode)) {
                $result['mapping']['resources']->push($data);
                continue;
            }
            $resource_ids = $this->resourceMap->get($resourceCode);

            $breakdownResources = BreakdownResource::where('code', $activityCodes)
                ->whereIn('resource_id', $resource_ids)->get();

            /** @var Collection $breakdownResources */
            if ($breakdownResources->count() == 1) {
                $breakdownResource = $breakdownResources->first();

                $unit_id = $this->unitsMap->get(mb_strtolower($data[9]));
                $resource = $breakdownResource->resource;
                if ($unit_id != $resource->unit) {
                    $result['units']->push($data);
                    continue;
                }

                ActualResources::create([
                    'project_id' => $this->project->id,
                    'wbs_level_id' => $breakdownResource->breakdown->wbs_level_id,
                    'breakdown_resource_id' => $breakdownResource->id,
                    'period_id' => $this->active_period->id,
                    'qty' => abs($data[10]),
                    'unit_price' => $data[11],
                    'cost' => abs($data[12]),
                    'unit_id' => $unit_id,
                    'action_date' => $excelBaseDate->addDays($data[5]),
                ]);

                ++$result['success'];
            } elseif (!$breakdownResources->count()) {
                $result['invalid']->push($data);
            } else {
                $breakdownResources->load('shadow');
                $data['resources'] = $breakdownResources->pluck('shadow');
                $result['multiple']->put($activityCode, $data);
            }
        }

        foreach (['mapping', 'resources', 'units', 'multiple', 'invalid'] as $key) {
            if ($result[$key]->count()) {
                $result['hasIssues'] = true;
                break;
            }
        }

        return $result;
    }

    protected function loadActivityMap()
    {
        $this->activityMap = collect();
        ActivityMap::where('project_id', $this->project->id)
            ->select('activity_code', 'equiv_code')->get()->each(function ($item) {
                $code = strtolower($item->activity_code);
                $equiv_code = strtolower($item->equiv_code);

                $this->activityMap->put($equiv_code, $code)->put($code, $code);
            });


        $this->activityCodes = collect();
        BreakdownResource::whereHas('breakdown', function ($q) {
            $q->where('project_id', $this->project->id);
        })->select(['id', 'code'])->get()->reduce(function (Collection $collection, $resource) {
            $code = strtolower($resource->code);
            if (!$collection->has($code)) {
                $collection->put($code, collect());
            }

            $collection->get($code)->push($resource->id);
            return $collection;
        }, collect());
    }

    protected function loadResourceMap()
    {
        $this->resourceMap = collect();

        Resource::select('id', 'resource_code')->all()->each(function(Resources $resource){
            $code = strtolower($resource->resource_code);
            if (!$this->resourceMap->has($code)) {
                $this->resourceMap->put($code, collect());
            }
            $this->resourceMap->get($code)->push($resource->id);
        });

        ResourceCode::select('resource_id', 'code')->get()->each(function ($resource_code) {
            $code = strtolower($resource_code->code);
            if (!$this->resourceMap->has($code)) {
                $this->resourceMap->put($code, collect());
            }
            $this->resourceMap->get($code)->push($resource_code->resource_id);
        });
    }

    protected function loadUnits()
    {
        $this->unitsMap = collect();

        UnitAlias::all()->each(function (UnitAlias $alias) {
            $this->unitsMap->put(mb_strtolower($alias->name), $alias->unit_id);
        });

        Unit::all()->each(function (Unit $unit) {
            $this->unitsMap->put(mb_strtolower($unit->type), $unit->id);
        });
    }
}
