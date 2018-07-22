<section id="ReportsArea" class="project-tab row">
    @if (empty($skipButtons))
    <div class="col-sm-12">
        <div class="form-group pull-right">
            <a href="{{route('break_down.printall',$project)}}?print=1&all=1" target="_blank" class="btn btn-primary">
                <i class="fa fa-print"></i> Print All
            </a>

            @can('budget_owner', $project)
                <a href="{{route('communication.budget', $project)}}" class="btn btn-primary in-iframe" title="Send Reports">
                    <i class="fa fa-send"></i> Send Reports
                </a>
            @endcan
        </div>
    </div>
    @endif

    <div class="col-sm-4">
        <a href="{{route('project.budget-checklist', $project)}}" class="btn btn-success btn-block">BUDGET CHECK LIST</a>
        <a href="{{route('wbs.report', $project)}}" target="_blank" class="btn btn-success btn-block">WBS (CONTROL POINT)</a>
        <a href="{{route('project.charter-report', $project)}}" target="_blank" class="btn btn-success btn-block">PROJECT CHARTER</a>
        <a href="{{route('project.profitability-report', $project)}}" target="_blank" class="btn btn-success btn-block">PROFITABILITY INDEX</a>
        <a href="{{route('stdActivity.report', $project)}}" target="_blank" class="btn btn-success btn-block">STANDARD ACTIVITY</a>
        <a href="{{route('productivity.report', $project)}}" target="_blank" class="btn btn-success btn-block">PRODUCTIVITY</a>
        <a href="{{route('qsReport.report', $project)}}" target="_blank" class="btn btn-success btn-block">QS SUMMARY</a>
        <a href="{{route('project.budget-trend', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET TREND</a>
        <a href="#" class="btn btn-success btn-block">REFERENCES &amp; NOTES</a>

    </div>

    <div class="col-sm-4">
        <a href="{{route('resource_dictionary.report', $project)}}" target="_blank" class="btn btn-success btn-block">RESOURCE DICTIONARY</a>
        <a href="{{route('high_priority.report', $project)}}" target="_blank" class="btn btn-success btn-block">HIGH PRIORITY MATERIALS</a>
        <a href="{{route('man_power.report', $project)}}" target="_blank" class="btn btn-success btn-block">LABOUR BUDGET (COST-UNIT)</a>
        <a href="{{route('wbs_labours_report', $project)}}" target="_blank" class="btn btn-success btn-block">MANDAY BY CONTROL POINT</a>
        <a href="{{route('budget_summary.report', $project)}}" target="_blank" class="btn btn-success btn-block">STANDARD ACTIVITY COST</a>
        <a href="{{route('activity_resource_breakdown.report', $project)}}" target="_blank" class="btn btn-success btn-block">ACTIVITY RESOURCE BREAKDOWN</a>
        <a href="{{route('wbs_dictionary_report', $project)}}" target="_blank" class="btn btn-success btn-block">WBS DICTIONARY</a>
        <a href="{{route('revised_boq.report', $project)}}" target="_blank" class="btn btn-success btn-block">REVISED BOQ (EAC Contract)</a>
        <a href="{{route('boq_price_list.report', $project)}}" target="_blank" class="btn btn-success btn-block">BOQ PRICE LIST</a>
    </div>

    <div class="col-sm-4">
        <a href="{{route('budget_cost_by_building.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST BY BUILDING</a>
        <a href="{{route('budget_cost_by_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST BY DISCIPLINE</a>
        <a href="{{route('budget_cost_vs_break_down.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST BY ITEM BREAKDOWN</a>
        <a href="{{route('budget_cost_dry_cost.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST V.S DRY COST BY BUILDING</a>
        <a href="{{route('budget_cost_dry_cost_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST V.S DRY COST BY DISCIPLINE</a>
        <a href="{{route('qty_cost_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">BUDGET COST V.S DRY COST QTY & COST</a>
        <a href="{{route('project.comparison', $project)}}" target="_blank" class="btn btn-success btn-block">COMPARISON REPORT</a>
    </div>
</section>