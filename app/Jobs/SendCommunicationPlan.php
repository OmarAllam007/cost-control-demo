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

    /** @var Collection */
    private $sheets;

    public function __construct(CommunicationSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function handle()
    {
        if ($this->schedule->sent_at) {
            return false;
        }

        $users = $this->schedule->users()->notSent()->with('report')->get();
        $this->sheets = $this->buildReports($users);

        $users->each(function ($user) {
            \Mail::send("mail.communication-plan.{$this->schedule->type}", ['user' => $user], function (Message $msg) use ($user) {
                $attachment = $this->buildReports($user);
                $msg->to($user->email);
                $msg->subject("[KPS {$this->schedule->type}] " . $this->schedule->project->name);
                $msg->attach($attachment['full'], ['as' => $this->schedule->project->name . '_' . $this->schedule->type . '_reports.xlsx']);
            });

            $user->send_at = Carbon::now();
        });

        $this->schedule->sent_at = Carbon::now();
        $this->schedule->save();
    }

    private function buildReports($user)
    {
        $report_ids = $user->reports()->notSent()->pluck('report_id')->unique();
        $reports = Report::find($report_ids);

        return \Excel::create('kps_reports', function(LaravelExcelWriter $writer) use ($reports) {
            foreach ($reports as $r) {
                $class_name = $r->class_name;
                $report = new $class_name($this->schedule->project);
                $writer->sheet(function(LaravelExcelWorksheet $sheet) use ($report) {
                    return $report->sheet($sheet);
                });
            }
        })->store($ext = 'xlsx', $path = storage_path('app'), $returnInfo = true);
    }
}
