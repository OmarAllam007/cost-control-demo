<section id="CostControlReports" class="project-tab">
    <table class="table table-condensed table-striped table-fixed text-center">
        <thead>
        {{--<div class="pull-right" style="padding: 5px;">--}}
        {{--<a href="{{route('break_down.printall',$project)}}?print=1&all=1" target="_blank" class="btn btn-primary">--}}
        {{--<li class="fa fa-print"></li>--}}
        {{--Print All</a>--}}
        {{--</div>--}}
        <tr id="buttons">
            <th class="col-xs-6">
                <a href="{{route('cost_control.info',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">
                    Project Information</a><br>
                <a href="{{route('cost_control.cost-summery',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Cost Summery</a><br>
                <a href="{{route('productivity.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Significant Material</a><br>
                <a href="{{route('qsReport.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Standard Activity</a><br>
                <a href="#" class="hvr-float-shadow btn btn-primary" target="" style="width:100%;margin-bottom: 8px;">BOQ</a><br>
                <a href="{{route('boq_price_list.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Overdraft</a><br>
                <a href="{{route('resource_dictionary.report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">RESOURCE Code</a><br>
            </th>
            <th class="col-xs-6">
                <a href="{{route('high_priority.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Activity</a><br>
                <a href="{{route('man_power.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Resource Dictionary</a><br>
                <a href="{{route('budget_summery.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Dashboard</a><br>
                <a href="{{route('activity_resource_breakdown.report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary" style="width:100%;margin-bottom: 8px;">Variance Analysis</a><br>
                <a href="{{route('revised_boq.report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Productivity
                </a><br>
                <a href="{{route('budget_cost_by_building.report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Labor Trend Analysis</a><br>
                <a href="{{route('budget_cost_by_discipline.report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Issues</a><br>

            </th>
        </tr>
        </thead>
    </table>
</section>

