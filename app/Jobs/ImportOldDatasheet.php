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
//        ActualResources::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        $counter = 1;
        $fh = fopen(storage_path('app/failed_old_data' . slug($this->project->name) . '.csv'), 'w');
        \DB::beginTransaction();
        foreach ($rows as $row) {
            $rowData = $this->getDataFromCells($row->getCellIterator());
            ++$counter;
            if (!array_filter($rowData)) {
                continue;
            }

            $entry = $this->getEntry($rowData);
            if ($entry) {
                ActualResources::create($entry);
                ++$success;
            } else {
                $failed->put($counter, $rowData);
                fputcsv($fh, $rowData);
            }

        }
        \DB::commit();

        fclose($fh);

        dispatch(new UpdateResourceDictJob($this->project));

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

        if (empty($row[9]) && empty($row[10]) && empty($row[11])) {
            $row[9] = $row[12];
            $row[10] = $row[13];
            $row[11] = $row[14];
        }

        $resource = [
            'project_id' => $this->project->id, 'period_id' => $this->period_id, 'batch_id' => $this->batch->id,
            'wbs_level_id' => $shadow->wbs_id, 'resource_id' => $shadow->resource_id, 'breakdown_resource_id' => $shadow->breakdown_resource_id,
            'original_code' => $row[2], 'cost' => $row[12], 'qty' => $row[13], 'unit_price' => $row[14],
        ];

        $shadow->progress = floatval($row[4]);
        $shadow->status = $row[5];

        if ($shadow->progress == 100) {
            $shadow->status = 'Closed';
        } elseif ($shadow->progress > 0) {
            $shadow->status = 'In Progress';
        }

        if (strtolower($shadow->status) == 'closed') {
            $shadow->progress = 100;
        }

        $shadow->save();

        return $resource;
    }

    protected function loadShadows()
    {
        $this->shadows = collect();
        $doubles = collect();
        BreakDownResourceShadow::where('project_id', $this->project->id)
            ->select(['id', 'code', 'cost_account', 'resource_code', 'remarks', 'wbs_id', 'resource_id', 'breakdown_resource_id', 'project_id'])
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
