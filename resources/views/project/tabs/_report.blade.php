<section id="ReportsArea" class="project-tab row">
    <div class="col-sm-12">
        <div class="form-group pull-right">
            <a href="{{route('break_down.printall',$project)}}?print=1&all=1" target="_blank" class="btn btn-primary">
                <i class="fa fa-print"></i> Print All
            </a>
        </div>
    </div>

    <div class="col-sm-4">
        <a href="{{route('project.budget-checklist', $project)}}" class="btn btn-success btn-block">Budget Check List</a>
        <a href="{{route('wbs.report', $project)}}" target="_blank" class="btn btn-success btn-block">WBS (CONTROL POINT)</a>
        <a href="{{route('project.charter-report', $project)}}" target="_blank" class="btn btn-success btn-block">Project Charter</a>
        <a href="{{route('project.profitability-report', $project)}}" target="_blank" class="btn btn-success btn-block">Profitability Index</a>
        <a href="{{route('stdActivity.report', $project)}}" target="_blank" class="btn btn-success btn-block">STANDARD ACTIVITY</a>
        <a href="{{route('productivity.report', $project)}}" target="_blank" class="btn btn-success btn-block">PRODUCTIVITY</a>
        <a href="{{route('qsReport.report', $project)}}" target="_blank" class="btn btn-success btn-block">QS Summary</a>
        <a href="{{route('project.budget-trend', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Trend</a>
        <a href="#" class="btn btn-success btn-block">REFERENCES &amp; NOTES</a>
        <a href="{{route('boq_price_list.report', $project)}}" target="_blank" class="btn btn-success btn-block">BOQ PRICE LIST</a>
    </div>

    <div class="col-sm-4">
        <a href="{{route('resource_dictionary.report', $project)}}" target="_blank" class="btn btn-success btn-block">RESOURCE DICITIONARY</a>
        <a href="{{route('high_priority.report', $project)}}" target="_blank" class="btn btn-success btn-block">High Priority Materials</a>
        <a href="{{route('man_power.report', $project)}}" target="_blank" class="btn btn-success btn-block">Labour Budget (Cost-Unit)</a>
        <a href="{{route('wbs_labours_report', $project)}}" target="_blank" class="btn btn-success btn-block">Manday by Control Point</a>
        <a href="{{route('budget_summary.report', $project)}}" target="_blank" class="btn btn-success btn-block">Standard Activity Cost</a>
        <a href="{{route('activity_resource_breakdown.report', $project)}}" target="_blank" class="btn btn-success btn-block">Activity Resource Breakdown</a>
        <a href="{{route('wbs_dictionary_report', $project)}}" target="_blank" class="btn btn-success btn-block">WBS Dictionary</a>
        <a href="{{route('revised_boq.report', $project)}}" target="_blank" class="btn btn-success btn-block">Revised BOQ</a>
    </div>

    <div class="col-sm-4">
        <a href="{{route('budget_cost_by_building.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost By Building</a>
        <a href="{{route('budget_cost_by_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost by Discipline</a>
        <a href="{{route('budget_cost_vs_break_down.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost by Item Breakdown</a>
        <a href="{{route('budget_cost_dry_cost.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost v.s Dry Cost By Building</a>
        <a href="{{route('budget_cost_dry_cost_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost v.s Dry Cost By Discipline</a>
        <a href="{{route('qty_cost_discipline.report', $project)}}" target="_blank" class="btn btn-success btn-block">Budget Cost v.s Dry Cost QTY & Cost</a>
    </div>
</section>