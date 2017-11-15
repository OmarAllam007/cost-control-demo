<?php

namespace App\BoqFixer;

use App\Project;

class BoqFixer
{
    /** @var Project */
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}