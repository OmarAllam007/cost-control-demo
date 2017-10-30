<?php

namespace App\BoqFixer;

use App\Project;

class BoqChecker
{
    /** @var Project */
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}