<?php

namespace App\Http\Controllers;

use App\ActualRevenue;
use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\GlobalPeriod;
use App\Http\Requests;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Reports\Cost\GlobalReport;
use App\ResourceType;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class DashboardController extends Controller
{
    function index()
    {
        if (request()->exists('clear')) {
            \Cache::forget('global-report');
        }

        $data = \Cache::remember('global-report', Carbon::tomorrow(), function () {
            $report = new GlobalReport();
            return $report->run();
        });

        return view('dashboard.index', $data);
    }
}
