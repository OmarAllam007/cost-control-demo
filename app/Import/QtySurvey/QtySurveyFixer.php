<?php

namespace App\Import\QtySurvey;

use App\Boq;
use App\Project;
use App\Survey;
use Illuminate\Support\Collection;

class QtySurveyFixer
{
    /** @var Project */
    private $project;

    /** @var Collection */
    protected $surveys;

    /** @var int */
    protected $counter = 0;

    /** @var  array */
    private $data;

    function __construct(Project $project, Collection $surveys, $data)
    {
        $this->surveys = $surveys;
        $this->project = $project;
        $this->data = $data;
    }

    function fix()
    {
        $boq_ids = collect($this->data['budget_qty'])->keys()->unique();
        $boqs = Boq::whereIn('id', $boq_ids)->get()->keyBy('id');

        foreach ($this->data['budget_qty'] as $boq_id => $budget_qty) {
            $eng_qty = $this->data['eng_qty'][$boq_id];

            $boq = $boqs->get($boq_id);
            $survey = Survey::where('boq_id', $boq_id)->where('cost_account', $boq->cost_account)->first();
            $attributes = [
                'boq_id' => $boq_id, 'budget_qty' => $budget_qty, 'eng_qty' => $eng_qty, 'cost_account' => $boq->cost_account,
                'description' => $boq->description, 'unit_id' => $boq->unit_id, 'wbs_level_id' => $boq->wbs_id,
                'project_id' => $this->project->id, 'discipline' => $boq->type, 'item_code' => $boq->item_code
            ];

            if ($survey) {
                $attributes['budget_qty'] += $survey->budget_qty;
                $attributes['eng_qty'] += $survey->eng_qty;
                $survey->fill($attributes);
            } else {
                $survey = new Survey($attributes);
            }

            $survey->save();

            $this->surveys->get($boq_id)->each(function ($survey) { $survey->save(); });
        }
    }
}