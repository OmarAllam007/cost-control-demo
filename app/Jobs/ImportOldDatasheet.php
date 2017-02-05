<?php

namespace App\Jobs;

use App\ActualBatch;
use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Job;
use App\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ImportOldDatasheet extends ImportJob // implements ShouldQueue
{
    //use InteractsWithQueue, SerializesModels;

    /**
     * @var Project
     */
    protected $project;

    protected $file;

    /**
     * @var ActualBatch
     */
    protected $batch;

    protected $period_id;

    /** @var Collection */
    protected $shadows;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->period_id = $project->open_period()->id;
        $this->batch = ActualBatch::create([
            'user_id' => \Auth::id(), 'type' => 'Old Data', 'file' => $file, 'project_id' => $project->id,
            'period_id' => $this->period_id
        ]);

        $this->loadShadows();
    }

    public function handle()
    {
        set_time_limit(3600);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $failed = collect();
        $success = 0;

        CostShadow::flushEventListeners();
        ActualResources::flushEventListeners();


        $entries = collect();
        foreach ($rows as $row) {
            $rowData = $this->getDataFromCells($row->getCellIterator());
            if (!array_filter($rowData)) {
                continue;
            }

            $entry = $this->getEntry($rowData);
            if ($entry) {
                $entries->push($entry);

                if ($entries->count() >= 500) {
                    CostShadow::insert($entries->pluck('shadow')->toArray());
                    ActualResources::insert($entries->pluck('resource')->toArray());
                    $success += $entries->count();
                    unset($entries);
                    $entries = collect();
                }
            } else {
                $failed->push($rowData);
            }

        }

        if ($entries->count()) {
            CostShadow::insert($entries->pluck('shadow')->toArray());
            ActualResources::insert($entries->pluck('resource')->toArray());
            $success += $entries->count();
            unset($entries);
        }

        return compact('success', 'failed');
    }

    protected function getEntry($row)
    {
        $code = mb_strtolower(trim($row[0]) . trim($row[1]) . trim($row[2]));
        if (!$this->shadows->has($code)) {
            $remark = trim($row[3]);
            if ($remark) {
                $code .= mb_strtolower(trim($row[3]));
            }
        }

        if ($this->shadows->has($code)) {
            $shadow = $this->shadows->get($code);
        } elseif (!empty($row[36])) {
            $appid = trim($row[36]);
            if (!$appid) {
                return null;
            }

            $shadow = $this->shadows->where('breakdown_resource_id', $appid)->first();
        }

        if (empty($shadow)) {
            return null;
        }

        $resource = [
            'project_id' => $this->project->id, 'period_id' => $this->period_id, 'batch_id' => $this->batch->id,
            'wbs_level_id' => $shadow->wbs_id, 'resource_id' => $shadow->resource_id, 'breakdown_resource_id' => $shadow->breakdown_resource_id,
            'original_code' => $row[2], 'qty' => $row[10], 'unit_price' => $row[11], 'cost' => $row[10]
        ];

        $shadow->progress = $row[4];
        $shadow->status = $row[5];
        $shadow->save();

        $shadow = [
            'project_id' => $this->project->id, 'period_id' => $this->period_id, 'batch_id' => $this->batch->id,
            'wbs_level_id' => $shadow->wbs_id, 'resource_id' => $shadow->resource_id, 'breakdown_resource_id' => $shadow->breakdown_resource_id,
            'previous_cost' => $row[6], 'previous_qty' => $row[7], 'previous_unit_price' => $row[8],
            'current_cost' => $row[9], 'current_qty' => $row[10], 'current_unit_price' => $row[11],
            'to_date_cost' => $row[12], 'to_date_qty' => $row[13], 'to_date_unit_price' => $row[14],
            'remaining_cost' => $row[15], 'remaining_qty' => $row[16], 'remaining_unit_price' => $row[17],
            'completion_qty' => $row[18], 'completion_cost' => $row[19], 'completion_unit_price' => $row[20],
            'allowable_var' => $row[21], 'allowable_ev_cost' => $row[22], 'bl_allowable_cost' => $row[23], 'bl_allowable_var' => $row[24],
            'qty_var' => $row[25], 'cost_var' => $row[26], 'unit_price_var' => $row[27], 'physical_unit' => $row[28], 'pw_index' => $row[29],
            'cost_variance_to_date_due_unit_price' => $row[30], 'allowable_qty' => $row[31],
            'cost_variance_remaining_due_unit_price' => $row[32], 'cost_variance_completion_due_unit_price' => $row[33],
            'cost_variance_completion_due_qty' => $row[34], 'cost_variance_to_date_due_qty' => $row[35],
        ];

        return compact('shadow', 'resource');
    }

    protected function loadShadows()
    {
        $this->shadows = collect();
        $doubles = collect();
        BreakDownResourceShadow::where('project_id', $this->project->id)
            ->select(['code', 'cost_account', 'resource_code', 'remarks', 'wbs_id', 'resource_id', 'breakdown_resource_id', 'project_id'])
            ->get()->each(function (BreakDownResourceShadow $resource) use ($doubles) {
                $code = mb_strtolower(trim($resource->code) . trim($resource->cost_account) . trim($resource->resource_code));
                if ($this->shadows->has($code)) {
                    //Modify the original code
                    $other = $this->shadows->get($code);
                    $otherCode = $code;
                    $otherCode .= mb_strtolower(trim($other->remarks));
                    $this->shadows->put($otherCode, $other);
                    $this->shadows->forget($code);
                    $doubles->put($code, $code);
                }

                if ($doubles->has($code)) {
                    $code .= mb_strtolower(trim($resource->remarks));
                }

                $this->shadows->put($code, $resource);
            });
    }
}
