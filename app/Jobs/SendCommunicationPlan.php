<?php

namespace App\Jobs;

use App\CommunicationSchedule;
use App\Report;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class SendCommunicationPlan extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $schedule;

    private $type = 'budget';

    public function __construct(CommunicationSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function handle()
    {
        if ($this->schedule->sent_at) {
            return false;
        }

        $this->type = snake_case(strtolower($this->schedule->type));

        $users = $this->schedule->users()->notSent()->with('reports')->get();

        \Log::info($this->schedule->period);

        $users->each(function ($user) {
            \Mail::send("mail.communication-plan.{$this->type}", [
                'user' => $user,
                'project' => $this->schedule->project,
                'period' => $this->schedule->period
            ], function (Message $msg) use ($user) {
                $attachment = $this->buildReports($user);
                $msg->to($user->user->email);
                $msg->subject("[KPS {$this->schedule->type}] " . $this->schedule->project->name);
                $msg->attach($attachment['full'], [
                    'as' => slug($this->schedule->project->name) . '_' . $this->type . '_reports.xlsx'
                ]);
            });

            $user->sent_at = Carbon::now();
            $user->save();
        });

        $this->schedule->sent_at = Carbon::now();
        $this->schedule->save();
    }

    private function buildReports($user)
    {

        $report_ids = $user->reports()->pluck('report_id')->unique();
        $reports = Report::find($report_ids->toArray());

        return \Excel::create('kps_reports', function(LaravelExcelWriter $writer) use ($reports) {
            foreach ($reports as $r) {
                $class_name = $r->class_name;
                if ($this->schedule->type == 'Budget') {
                    $report = new $class_name($this->schedule->project);
                } else {
                    $report = new $class_name($this->schedule->period);
                }

                $writer->sheet($r->name, function(LaravelExcelWorksheet $sheet) use ($report) {
                    return $report->sheet($sheet);
                });
            }

            $writer->setActiveSheetIndex(0);
        })->store($ext = 'xlsx', $path = storage_path('app'), $returnInfo = true);
    }
}
