<?php

namespace App\Jobs;

use App\Breakdown;
use App\Jobs\Job;
use App\Project;
use App\StdActivityResource;
use function GuzzleHttp\Psr7\str;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class EasyUploadJob extends ImportJob
{
    /** @var Project */
    protected $project;

    /** @var string */
    protected $file;

    /** @var Collection */
    protected $wbs_codes;

    /** @var Collection */
    protected $templates;

    /** @var Collection */
    protected $cost_accounts;

    protected $status = ['success' => 0];

    /** @var int */
    protected $errorFieldIndex;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;

        $this->status['failed'] = collect();
    }

    public function handle()
    {
        $this->loadWbsCodes();
        $this->loadTemplates();
        $this->loadCostAccounts();

        /** @var \PHPExcel $excel */
        $excel = \PHPExcel_IOFactory::load($this->file);

        $sheet = $excel->getSheet(0);

        $this->errorFieldIndex = ord($sheet->getHighestColumn()) - ord('A');

        $rows = $sheet->getRowIterator(2);

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            $this->importRow($data);
        }


        return $this->status;
    }

    protected function loadWbsCodes()
    {
        $this->wbs_codes = collect(\DB::table('wbs_levels')
            ->where('project_id', $this->project->id)
            ->get(['id', 'code', 'parent_id']))
            ->keyBy(function ($level) {
                return strtolower($level->code);
            });
    }

    protected function loadTemplates()
    {
        $this->templates = $this->project->templates()
            ->with('resources')->get()
            ->keyBy(function ($template) {
                return strtolower($template->code);
            });
    }

    protected function importRow($data)
    {
        $wbs_code = strtolower($data[0]);
        $template_code = strtolower($data[1]);
        $cost_account = strtolower($data[2]);

        $level = $this->wbs_codes->get($wbs_code);
        $template = $this->templates->get($template_code);

        if (!$this->validateRow($data,$level, $template, $cost_account)) {
            return false;
        }

        $breakdown = Breakdown::create([
            'project_id' => $this->project->id, 'wbs_level_id' => $level->id,
            'template_id' => $template->id, 'std_activity_id' => $template->std_activity_id,
            'cost_account' => $cost_account
        ]);

        $template->resources->each(function(StdActivityResource $resource) use ($breakdown) {
            $breakdown->resources()->create([
                'std_activity_resource_id' => $resource->id,
                'resource_id' => $resource->resource_id,
                'equation' => $resource->equation
            ]);
        });

        $breakdown->syncVariables($this->getVariables($data));

        $this->status['success'] ++;
    }

    protected function loadCostAccounts()
    {
        $this->cost_accounts = collect(\DB::table('qty_surveys')
            ->where('project_id', $this->project->id)
            ->get(['id', 'cost_account', 'wbs_level_id']))
            ->groupBy('wbs_level_id')->map(function($items) {
                return $items->keyBy(function($item) {
                    return strtolower($item->cost_account);
                });
            });
    }

    protected function checkIfCostAccountOnWbs($cost_account, $wbs)
    {
        if ($this->cost_accounts->has($wbs->id) && $this->cost_accounts->get($wbs->id)->has($cost_account)) {
            return true;
        }

        $parent = $this->wbs_codes->where('id', $wbs->parent_id)->first();
        if (!$parent) {
            return false;
        }

        return $this->checkIfCostAccountOnWbs($cost_account, $parent);
    }

    protected function validateRow($data, $level, $template, $cost_account)
    {
        $error = '';
        if (!$level) {
            $error = 'WBS Level not found';
        }

        if (!$template) {
            $error = 'Template not found';
        }

        if ($level && !$this->checkIfCostAccountOnWbs($cost_account, $level)) {
            $error = 'Cost account not found on WBS';
        }

        if ($error) {
            $data[$this->errorFieldIndex] = $error;
            $this->status['failed']->push($data);
            return false;
        }

        return true;
    }

    protected function getVariables($data)
    {
        $index = 1;
        $vars = [];
        for ($i = 3; $i < count($data); ++$i) {
            $vars[$index] = floatval($data[$i]);
            ++$index;
        }
        return $vars;
    }

}
