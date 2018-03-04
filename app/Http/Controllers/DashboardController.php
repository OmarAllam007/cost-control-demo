<?php

namespace App\Http\Controllers;

use App\GlobalPeriod;
use App\Period;
use App\Reports\Cost\GlobalReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /** @var Collection */
    private $globalPeriods;

    function index(Request $request)
    {
        $this->globalPeriods = $this->getGlobalPeriods();

        $period = $this->getPeriod($request);

        $key = 'global-report-' . $period->id;

        if ($request->exists('clear')) {
            \Cache::forget($key);
        }


        $data = \Cache::remember($key, Carbon::tomorrow(), function () use ($period) {
            $report = new GlobalReport($period);
            return $report->run();
        });

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
        return GlobalPeriod::latest('end_date')->get()->filter(function($period) {
            return Period::where('global_period_id', $period->id)->where('status', Period::GENERATED)->exists();
        });
    }
}
