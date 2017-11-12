<?php

namespace App\Import\QtySurvey;

use App\Boq;
use App\Project;
use App\Survey;
use Illuminate\Support\Collection;

class QtySurveyChecker
{
    /** @var Project */
    private $project;

    /** @var Collection */
    private $surveys;

    /** @var Collection */
    private $failed;

    /** @var int */
    protected $counter = 0;

    /** @var Collection */
    protected $boqs;

    public function __construct(Project $project, Collection $surveys, Collection $failed = null)
    {
        $this->project = $project;
        $this->surveys = $surveys;
        $this->failed = $failed ?: collect();
    }

    function check()
    {
        $this->boqs = $this->surveys->map(function($survey) {
            $boq_id = Boq::forQs($survey)->value('id');
            $survey->boq_id = $boq_id;

            return $survey;
        })->groupBy('boq_id')->reject(function(Collection $group)  {
            if ($group->count() > 1) {
                return false;
            }

            $survey = $group->first();

            $hasOtherSurveys = Survey::where('boq_id', $survey->boq_id)->exists();
            if ($hasOtherSurveys) {
                return false;
            }

            if ($survey->unit != $survey->boq->id) {
                return false;
            }

            $survey->save();

            // Create equivalent QS
            Survey::craete([
                'wbs_level_id' => $survey->boq->wbs_id, 'cost-account' => $survey->boq->cost_account,
                'description' => $survey->boq->description, 'boq_id' => $survey->boq_id, 'unit_id' => $survey->unit,
                'budget_qty' => $survey->budget_qty, 'eng_qty' => $survey->eng_qty
            ]);

            return true;
        });

        return $this->redirect();
    }

    protected function generateFailedExport()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();

        $headers = ['WBS Code', 'Item Code', 'Description', 'Budget Qty', 'Eng. Qty', 'Unit', 'Errors'];
        $sheet->fromArray($headers, null, 'A1', true);

        $counter = 2;
        foreach ($this->failed as $row) {
            $sheet->fromArray($row, null, "A{$counter}", true);
            ++$counter;
        }

        $sheet->getStyle("D2:E{$counter}")->getNumberFormat()->setFormatCode('#,##0.00');

        $filename = storage_path('app/public/qs-failed-' . date('YmdHis') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return '/storage/' . basename($filename);
    }

    protected function redirect()
    {
        $iframe = request('iframe')? '?iframe=1' : '';
        $failed = '';
        if ($this->failed->count()) {
            $failed = $this->generateFailedExport();
        }

        if ($this->boqs->count()) {
            $cacheKey = uniqid('qs-boq-map-', true);
            \Cache::put($cacheKey, ['boqs' => $this->boqs, 'success' => $this->counter, 'project' => $this->project, 'failed' => $failed], 1440);
            return redirect(route('qty-survey.fix-boq', $cacheKey) . $iframe);
        }

        if ($this->failed->count()) {
            return view('survey.import-failed', ['success' => $this->counter, 'project' => $this->project, 'failed' => $failed]);
        }

        flash("{$this->counter} Qty survey items have been imported");
        if ($iframe) {
            return \Redirect::to('/blank?reload=quantities');
        }

        return \Redirect::route('project.budget', $this->project);
    }
}