<?php

namespace App\Jobs;

use App\Project;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;

class SendMappingErrorNotification
{

    protected $data;
    protected $type;
    /** @var Project */
    protected $project;

    function __construct($project, $data, $type)
    {
        $this->data = $data;
        $this->type = $type;
        $this->project = $project;
    }

    function handle()
    {
        $type = $this->type;

        if (!$this->project->cost_owner) {
            return false;
        }

        \Mail::send('mail.mapping.' . $this->type, compact('project', 'type'), function(Message $message) {
            $to = [$this->project->cost_owner->email];
            if ($this->type == 'resources') {
                $to[] = $this->project->owner->email;
            }
            $message->to($to);
            $message->subject('[KPS]' . Str::studly($this->type) . ' Mapping Error');
            $message->cc(\Auth::user()->email);

            $filename = $this->createExcelFile();
            $attachmentName = slug($this->project->name) . '_' . $this->type . '_mapping_' . date('Ymdhi') . '.xlsx';
            $message->attach($filename, ['as' => $attachmentName]);
        });
    }

    protected function createExcelFile()
    {
        $rows = $this->data[$this->type];

        $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $reader->load(public_path('files/templates/actual.xlsx'));

        $sheet = $excel->getSheet(0);

        foreach ($rows as $i => $row) {
            $sheet->fromArray($row, '', 'A' . ($i + 2));
        }

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $filename = storage_path('app/' . uniqid() . '.xlsx');
        $writer->save($filename);

        return $filename;
    }
}