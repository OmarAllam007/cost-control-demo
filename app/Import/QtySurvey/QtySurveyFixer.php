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

    /** @var Collection */
    protected $failed;

    public function __construct(Project $project, Collection $surveys, Collection $failed)
    {
        $this->surveys = $surveys;
        $this->failed = $failed;
        $this->project = $project;
    }
}