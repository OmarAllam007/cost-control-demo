<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/15/17
 * Time: 10:35 PM
 */

namespace App\Http\Controllers;


use App\Jobs\ExportCostToMaster;
use App\Period;

class PeriodReportsController extends Controller
{

    function store(Period $period)
    {
        if (!can('cost_owner', $period->project)) {
            flash('You are not authorized to do this cation');
            return redirect()->back();
        }

        if (Period::GENERATING == $period->status) {
            flash('Reports are already being generated for this period', 'warning');
            return redirect()->back();
        }

        $period->update(['status' => Period::GENERATING]);

        $this->dispatch(new ExportCostToMaster($period));

        flash('Reports will be generated for period ' . $period->name, 'success');
        return redirect()->back();
    }
}