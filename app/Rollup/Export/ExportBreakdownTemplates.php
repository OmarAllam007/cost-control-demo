<?php
namespace App\Rollup\Export;

use App\BreakdownTemplate;

class ExportBreakdownTemplates
{
    private $project;
    private $excel;
    private $sheet;

    private $counter = 1;

    public function __construct($project) {
        $this->project = $project;
        $this->excel = new \PHPExcel();
        $this->sheet = $this->excel->getActiveSheet();
        $this->setHeaders();
    }

    function handle(Project $project = null)
    {
        $query = BreakdownTemplate::query()->with('resources.resource')->with('resources.productivity');

        if ($this->project) {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');
        }

        $templates = $query->get();
    
        $templates->each(function ($template) {
            $this->addTemplate($template);
        });
        
        $this->setFormats();
        
        $filename = $this->getName();
        \PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007')->save($filename);
        return $filename;
    }

    private function addTemplate($template)
    {
        foreach ($template->resources as $resource) {
            $this->sheet->fromArray([
                $template->id,  // A
                $resource->id,  // B
                $template->name, // C
                $resource->resource->resource_code, // D
                $resource->resource->name, // E
                $resource->productivity->csi_code ?? '',  // F
                $resource->labor_count, // G
                $resource->remarks, // H
                $resource->equation, // I
                $resource->important ? '*' : null // J
            ], null, "A{$this->counter}", true);

            ++$this->counter;
        }
    }

    private function setHeaders()
    {
        $this->sheet->setTitle('Breakdown Templates');

        $this->sheet->fromArray([
            'Template App Code', // A
            'Resource App Code',  // B
            'Template Name',  // C
            'Resource Code', // D
            'Resource Name',  // E
            'Productivity Ref', // F
            'Labours Count',  // G
            'Remarks', // H
            'Equation', // I
            'Important?' // J
        ], null, "A{$this->counter}");
        ++$this->counter;
    }

    private function setFormats()
    {
        
    }

    private function getName()
    {
        if ($this->project) {
            return storage_path('app/breakdown_templates-' . str_slug($this->project->name) . '.xlsx');
        } 

        return storage_path('app/breakdown_templates.xlsx');
    }
}