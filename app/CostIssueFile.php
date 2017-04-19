<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostIssueFile extends Model
{
    protected $fillable = ['subject', 'period_id', 'file', 'description'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    function getFilePathAttribute()
    {
        return $this->uploadDir() . $this->file;
    }

    function getFileNameAttribute()
    {
        $tokens = explode('_', $this->file);
        array_shift($tokens);
        return implode('_', $tokens);
    }

    public function uploadDir()
    {
        return storage_path('app/cost-files/');
    }

    function url()
    {
        return "/project/{$this->project_id}/issue-files/{$this->id}";
    }
}
