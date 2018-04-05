<?php

namespace App\Jobs;

use App\CommunicationSchedule;
use App\Report;
use App\Reports\Cost\ProjectInfo;
use App\User;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use \Reflection;
use ReflectionClass;
use function unlink;

class SendCommunicationPlan extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $schedule;

    private $type = 'budget';

    private $cleanup = [];
    private $send_dashboard = false;

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

        $this->created_by = User::where('id', $this->schedule->created_by)->value('email');

        $users->each(function ($user) {
            \Mail::send("mail.communication-plan.{$this->type}", [
                'user' => $user,
                'project' => $this->schedule->project,
                'period' => $this->schedule->period
            ], function (Message $msg) use ($user) {
                $this->send_dashboard = false;
                $attachment = $this->buildReports($user);

                $msg->to($user->user->email);
                if ($this->created_by) {
                    $msg->cc($this->created_by);
                }
                $msg->subject("[KPS {$this->schedule->type}] " . $this->schedule->project->name);

                $msg->attach($attachment, [
                    'as' => slug($this->schedule->project->name) . '_' . $this->type . '_reports.xlsx'
                ]);

                if ($this->send_dashboard) {
                    $this->attachDashboard($msg);
                }
            });

            $user->sent_at = Carbon::now();
            $user->save();
        });

        $this->schedule->sent_at = Carbon::now();
        $this->schedule->save();

        $this->cleanFiles();
    }

    private function buildReports($user)
    {
        $report_ids = $user->reports()->pluck('report_id')->unique();
        $reports = Report::find($report_ids->toArray());

        \Config::set('excel.export.includeCharts', true);

        $info = \Excel::create('kps_reports', function(LaravelExcelWriter $writer) use ($reports) {
            foreach ($reports as $r) {
                $class_name = $r->class_name;

                if ($r->class_name == ProjectInfo::class) {
                    $this->send_dashboard = true;
                    continue;
                }

                if ($r->type == 'Budget') {
                    $report = new $class_name($this->schedule->project);
                    $writer->sheet($r->name, function(LaravelExcelWorksheet $sheet) use ($report, $r) {
                        $report->sheet($sheet);
                    });
                } else {
                    $report = new $class_name($this->schedule->period);
                    $class = new ReflectionClass($report);
                    $method = $class->getMethod('sheet');
                    if ($method->getParameters()) {
                        $writer->sheet($r->name, function(LaravelExcelWorksheet $sheet) use ($report) {
                            $report->sheet($sheet);
                        });
                    } else {
                        $writer->excel->addExternalSheet($report->sheet());
                    }
                }
            }

            $writer->setActiveSheetIndex(0);
        })->store($ext = 'xlsx', $path = storage_path('app'), $returnInfo = true);

        $this->cleanup[] = $info['full'];

        return $info['full'];
    }

    private function cleanFiles()
    {
        foreach ($this->cleanup as $file) {
            @unlink($file);
        }
    }

    private function attachDashboard($msg)
    {
        $report = new ProjectInfo($this->schedule->period);
        $pdf = $report->createPdf();
        if ($pdf) {
            $msg->attach($pdf, [
                'as' => slug($this->schedule->project->name) . '_dashboard.pdf'
            ]);

            $this->cleanup[] = $pdf;
        }
    }

}
