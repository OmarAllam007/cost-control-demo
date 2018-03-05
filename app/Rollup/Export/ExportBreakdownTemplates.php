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

    function handle()
    {
        $query = BreakdownTemplate::query()->with('resources.resource')->with('resources.productivity');

        if ($this->project) {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');
        }

        // Chunk templates in groups of thousands to save memory
        $templates = $query->chunk(1000, function ($templates) {
            $templates->each(function ($template) {
                $this->addTemplate($template);
            });
        });

        $this->setFormats();

        $filename = $this->getName();
        \PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007')->save($filename);
        return $filename;
    }

    private function addTemplate($template)
    {
        foreach ($template->resources as $resource) {
            $input = collect([
                $template->id,  // A
                $resource->id,  // B
                $template->activity->code, // C
                $template->activity->name, // D
                $template->code, // E
                $template->name, // F
                $resource->resource->resource_code, // G
                $resource->resource->name, // H
                $resource->productivity->csi_code ?? '',  // I
                $resource->labor_count, // J
                $resource->remarks, // K
                $resource->equation, // L
                $resource->important ? '*' : null, // M,
                '', // N: Errors cell
            ]);

            $divisions = $template->activity->division->parentsTree->each(function($div) use ($input) {
                $input->push($div->code . ' ' . $div->name);
            });

            $this->sheet->fromArray($input->toArray(), null, "A{$this->counter}", true);

            ++$this->counter;
        }
    }

    private function setHeaders()
    {
        $this->sheet->setTitle('Breakdown Templates');

        $this->sheet->fromArray([
            'Template App Code', // A
            'Resource App Code',  // B
            'Std Activity Code',  // C
            'Std Activity Name',  // D
            'Template Code', // E
            'Template Name',  // F
            'Resource Code', // G
            'Resource Name',  // H
            'Productivity Ref', // I
            'Labours Count',  // J
            'Remarks', // K
            'Equation', // L
            'Important?' // M
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