<?php
namespace App\Rollup\Import;

use App\BreakdownTemplate;
use App\StdActivityResource;
use App\Resources;
use App\Productivity;

/**
 * Import amd modify breakdown templates from Excel
 */
class BreakdownTemplateImporter
{
    private $rules = [
        'resource_id' => 'required|valid_resource', 
        'equation' => 'required',
        'productivity_id' => 'sometimes|valid_productivity'
    ];

    private $messages = [
        'valid_resource' => 'Invalid resource code',
        'valid_productivity' => 'Invalid productivity ref'
    ];

    private $success = 0;

    function __construct($file, $project = null)
    {
        $this->file = $file;
        $this->project = $project;

        $this->templates = collect();
        $this->resources = collect();
        $this->productivity = collect();
        $this->failed = collect();

        $this->initValidationRules();
    }

    function handle()
    {
        $excel = \PHPExcel_IOFactory::load($this->file);
        $rows = $excel->getActiveSheet()->getRowIterator(2);

        $template = null;
        foreach ($rows as $row) {
            $cells = $this->getDataFromCells($row);

            $this->importRow($cells);
        }

        $failed_file = '';
        if ($this->failed->count()) {
            $failed_file = $this->createFailedFile();
        }

        return ['project' => $this->project, 'failed' => $this->failed, 'success' => $this->success, 'failed_file' => $failed_file];
    }

    private function importRow($data) 
    {
        $template_id = intval($data['A']);
        $template = $this->templates->get($template_id);
        if (!$template) {
            $template = BreakdownTemplate::find($template_id);
            $this->templates->put($template->id, $template);

            if (!$template) {
                $data['N'] = "Template not found";
                $this->failed->push($data);
                return false;
            }
        }

        if (!$this->project) {
            $this->updateTemplate($template, $data);
        }

        $resource = StdActivityResource::find($data['B']);
        if (!$resource) {
            $data['N'] = "Resource not found";
            $this->failed->push($data);
            return false;
        }

        $attributes = [
            'resource_id' => $this->getResource($data['G']),
            'productivity_id' => $this->getProductivity($data['I']),
            'labor_count' => $data['J'],
            'remarks' => $data['K'],
            'equation' => $data['L'],
            'important' => !empty(trim($data['M']))
        ];

        $validator = validator($attributes, $this->rules, $this->messages);
        if ($validator->fails()) {
            $data['N'] = collect($validator->messages()->all())->implode("\n");
            $this->failed->push($data);
            return false;
        }

        $resource->fill($attributes)->save();
        ++$this->success;
    }

    private function updateTemplate($template, $data)
    {
        $code = strtolower($data['E']);
        $name = strtolower($data['F']);

        if (strtolower($template->code) !== $code) {
            $template->code = $data['E'];
        }

        if (strtolower($template->name) !== $name) {
            $template->name = $data['F'];
        }

        $update = $template->isDirty();

        $template->save();

        if ($update) {
            \DB::table('breakdown_templates')
                ->where('parent_template_id', $template->id)
                ->update(['code' => $template->code, 'name' => $template->name]);
        }
    }

    private function getResource($code)
    {
        $code = strtolower($code);
        $resource = $this->resources->get($code);

        if (!$resource) {
            $resource = Resources::whereNull('project_id')->whereResourceCode($code)->value('id');
            $this->resources->put($code, $resource);
        }

        return $resource;
    }

    private function getProductivity($code)
    {
        $code = strtolower($code);
        $productivity = $this->productivity->get($code);

        if (!$productivity) {
            $productivity = Productivity::whereNull('project_id')->whereCsiCode($code)->value('id');
            $this->productivity->put($code, $productivity);
        }

        return $productivity;
    }

    private function getDataFromCells($row) 
    {
        $cells = $row->getCellIterator();
        $data = [];

        foreach ($cells as $idx => $cell) {
            $data[$idx] = $cell->getValue();
        }

        return $data;
    }

    private function initValidationRules()
    {
        \Validator::extend('valid_resource', function($_, $value) {
            return Resources::whereNull('project_id')->whereId($value)->exists();
        });

        \Validator::extend('valid_productivity', function($_, $value) {
            return Productivity::whereNull('project_id')->whereId($value)->exists();
        });
    }

    private function createFailedFile()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet(); 
        $counter = 1;

        foreach ($this->failed as $row) {
            ++$counter;
            $sheet->fromArray($row, null, "A{$counter}", true);
        }

        $filename = storage_path('app/public/' . 'templates_failed_import_' . date('Ymd') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return basename($filename);
    }
}