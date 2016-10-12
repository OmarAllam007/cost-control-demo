<table class="table table-condensed table-striped table-fixed text-center">
    <thead>
    <tr>
        <th class="col-xs-4 text-center">Budget Calculations</th>
        <th class="col-xs-4 text-center">Budget Output</th>
        <th class="col-xs-4 text-center">Budget Reports</th>
    </tr>

    <tr>
        <th class="col-xs-4">

            <a href="{{route('wbs.report',$project)}}" class="btn btn-success" style="width:100%; margin-bottom: 2px;">WBS (CONTROL POINT)</a><br>
            <a href="{{route('stdActivity.report',$project)}}" class="btn btn-success" style="width:100%; margin-bottom: 2px;">STANDARD ACTIVITY</a><br>
            <a href="{{route('productivity.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">PRODUCTIVITY</a><br>
            <a href="{{route('qsReport.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">QS Summary</a><br>
            <a href="#" class="btn btn-success" style="width:100%;margin-bottom: 2px;">REFERENCES & NOTES</a><br>
            <a href="{{route('boq_price_list.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">BOQ PRICE LIST</a><br>
        </th>
        <th class="col-xs-4">
            <a href="{{route('resource_dictionary.report',$project)}}" class="btn btn-success" style="width:100%; margin-bottom: 2px;">RESOURCE DICITIONARY</a><br>
            <a href="{{route('high_priority.report',$project)}}" class="btn btn-success" style="width:100%; margin-bottom: 2px;">High Priority Materials</a><br>
            <a href="{{route('man_power.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Number (Manpower)</a><br>
            <a href="{{route('budget_summery.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Summary</a><br>
            <a href="{{route('activity_resource_breakdown.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Activity Resource Breakdown</a><br>
            <a href="{{route('revised_boq.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Revised BOQ
            </a><br>
        </th>
        <th class="col-xs-4">
            <a href="{{route('budget_cost_by_building.report',$project)}}" class="btn btn-success" style="width:100%; margin-bottom: 2px;">Budget Cost By Building</a><br>
            <a href="{{route('budget_cost_by_discipline.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Cost by Disicpline</a><br>
            <a href="{{route('budget_cost_vs_break_down.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Cost by Item Break Dowm
            </a><br>
            <a href="{{route('budget_cost_dry_cost.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost By Building
            </a><br>
            <a href="{{route('budget_cost_dry_cost_discipline.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost By Disicpline
            </a><br>
            <a href="{{route('qty_cost_discipline.report',$project)}}" class="btn btn-success" style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost QTY & Cost
            </a><br>
        </th>
    </tr>
    </thead>
</table>