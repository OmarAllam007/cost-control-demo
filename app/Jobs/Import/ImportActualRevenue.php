<?php

namespace App\Jobs\Import;

use App\ActualRevenue;
use App\Boq;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ImportActualRevenue extends ImportJob
{
    protected $file;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Period */
    protected $period;

    public function __construct($file, $period_id)
    {
        $this->file = $file;
        $this->period = Period::find($period_id);
        $this->project = $this->period->project;
        $this->wbs_levels = WbsLevel::where('project_id', $this->project->id)->get()->keyBy('code');
    }


    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        $failed = collect();

        $counter = 0;
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $wbs = $this->wbs_levels->get($data[0]);
            if (!$wbs) {
                $failed->push($data);
                continue;
            }

            $cost_account = $data[1];
            $boq = Boq::costAccountOnWbs($wbs, $cost_account)->first();
            if (!$boq) {
                $failed->push($data);
                continue;
            }

            $attributes = [ 'project_id' => $this->project->id, 'period_id' => $this->period->id,
                'wbs_id' => $wbs->id, 'cost_account' => $cost_account, 'boq_id' => $boq->id ];

            $record = ActualRevenue::firstOrCreate($attributes);
            $record->value = $data[2];

            $record->save();
            ++$counter;
        }

        $content = $failed->map(function($row) {
            return implode(',', array_map('csv_quote', $row));
        })->implode(PHP_EOL);

        file_put_contents(storage_path('app/failed_actual_revenue_' . $this->project->id . '.csv'), $content);

        return $counter;
    }
}
