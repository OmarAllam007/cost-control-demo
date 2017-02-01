<section id="ReportsArea" class="project-tab">
    <table class="table table-condensed table-striped table-fixed text-center">
        <thead>
        <div class="pull-right" style="padding: 5px;">
            <a href="{{route('break_down.printall',$project)}}?print=1&all=1" target="_blank" class="btn btn-primary">
                <li class="fa fa-print"></li>
                Print All</a>
        </div>

        <tr id="buttons">
            <th class="col-xs-4">
                <a href="{{route('wbs.report',$project)}}" target="_blank" class="btn btn-success threeD "
                   style="width:100%; margin-bottom: 2px;">WBS (CONTROL POINT)</a><br>
                <a href="{{route('stdActivity.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%; margin-bottom: 2px;">STANDARD ACTIVITY</a><br>
                <a href="{{route('productivity.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">PRODUCTIVITY</a><br>
                <a href="{{route('qsReport.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">QS Summary</a><br>
                <a href="#" class="btn btn-success threeD" target="" style="width:100%;margin-bottom: 2px;">REFERENCES &
                    NOTES</a><br>
                <a href="{{route('boq_price_list.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">BOQ PRICE LIST</a><br>
            </th>
            <th class="col-xs-4">
                <a href="{{route('resource_dictionary.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%; margin-bottom: 2px;">RESOURCE DICITIONARY</a><br>
                <a href="{{route('high_priority.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%; margin-bottom: 2px;">High Priority Materials</a><br>
                <a href="{{route('man_power.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Number (Manpower)</a><br>
                <a href="{{route('budget_summery.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Summary</a><br>
                <a href="{{route('activity_resource_breakdown.report',$project)}}" target="_blank"
                   class="btn btn-success threeD" style="width:100%;margin-bottom: 2px;">Activity Resource Breakdown</a><br>
                <a href="{{route('revised_boq.report',$project)}}" target="_blank" class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Revised BOQ
                </a><br>
            </th>
            <th class="col-xs-4">
                <a href="{{route('budget_cost_by_building.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%; margin-bottom: 2px;">Budget Cost By Building</a><br>
                <a href="{{route('budget_cost_by_discipline.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Cost by Discipline</a><br>
                <a href="{{route('budget_cost_vs_break_down.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Cost by Item Break Dowm
                </a><br>
                <a href="{{route('budget_cost_dry_cost.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost By Building
                </a><br>
                <a href="{{route('budget_cost_dry_cost_discipline.report',$project)}}" target="_blank"
                   class="btn btn-success threeD" style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost By
                    Discipline
                </a><br>
                <a href="{{route('qty_cost_discipline.report',$project)}}" target="_blank"
                   class="btn btn-success threeD"
                   style="width:100%;margin-bottom: 2px;">Budget Cost v.s Dry Cost QTY & Cost

                </a><br>
            </th>
        </tr>
        </thead>
    </table>
</section>