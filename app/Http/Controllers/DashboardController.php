<?php

namespace App\Http\Controllers;

use App\GlobalPeriod;
use App\Period;
use App\Reports\Cost\GlobalReport;
use Auth;
use function back;
use Carbon\Carbon;
use function compact;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Collection;
use Mail;
use function redirect;

class DashboardController extends Controller
{
    /** @var Collection */
    private $globalPeriods;

    function index(Request $request)
    {
        if (cannot('dashboard')) {
            return redirect('/projects');
        }

        $this->globalPeriods = $this->getGlobalPeriods();

        $period = $this->getPeriod($request);
        $report = new GlobalReport($period);

        if (request()->exists('pdf')) {
            return $report->pdf();
        }

        $data = $report->run();
        $data['globalPeriods'] = $this->globalPeriods;
        $data['reportPeriod'] = $period;

        return view('dashboard.index', $data);
    }

    private function getPeriod(Request $request)
    {
        $period = null;

        if ($period_id = $request->get('period')) {
            $request->session()->put('gloabl-report-period', $period_id);
        }

        if ($period_id = $request->session()->get('gloabl-report-period')) {
            $period = GlobalPeriod::find($period_id);
        } else {
            $period = $this->globalPeriods->first();
            $request->session()->put('gloabl-report-period', $period->id);
        }

        return $period;
    }

    private function getGlobalPeriods()
    {
        return GlobalPeriod::latest('end_date')->whereHas('periods', function ($query) {
            $query->where('status', Period::GENERATED);
        })->get();
    }

    function send()
    {
        $this->authorize('admin');

        $periods = GlobalPeriod::select('id', 'name')->latest('end_date')->get();

        return view('dashboard.send', compact('periods'));
    }

    function postSend(Request $request)
    {
        $this->authorize('admin');

        $this->validate($request, [
            'period_id' => 'required|exists:global_periods,id',
            'recipients.*.name' => 'required',
            'recipients.*.email' => 'required|email',
            'recipients' => 'required|array|min:1'
        ]);

        $period = GlobalPeriod::find($request->get('period_id'));

        $report = new GlobalReport($period);
        $file = $report->createPdf();

        if (!$file) {
            flash('Could not generate dashboard as PDF');
            return back();
        }

        foreach ($request->get('recipients') as $recipient) {
            Mail::send('mail.dashboard',
                compact('recipient', 'period'),
                function (Message $msg) use ($file, $recipient, $period) {
                    $msg->attach($file, ['as' => 'KPS Dashboard.pdf']);
                    $msg->to($recipient['email']);
                    $msg->cc(Auth::user()->email);
                    $msg->subject('KPS Dashboard - ' . $period->name);
                });
        }

        flash('Dashboard has been sent', 'success');
        return redirect('/dashboard');
    }
}
