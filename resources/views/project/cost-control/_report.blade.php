<section id="CostControlReports" class="project-tab">
    <table class="table table-condensed table-striped table-fixed text-center">
        <thead>

        <tr id="buttons">
            <th class="col-xs-6">
                <a href="{{route('cost_control.info',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">
                    Project Information</a><br>
                <a href="/project/{{$project->id}}/dashboard" target="_blank" class="hvr-float-shadow btn btn-primary" style="width:100%;margin-bottom: 8px;">Dashboard</a><br>
                <a href="{{route('cost.boq_report',$project)}}" target="_blank"  class="hvr-float-shadow btn btn-primary" target="" style="width:100%;margin-bottom: 8px;">BOQ</a><br>


                <a href="{{route('cost.overdraft',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary" style="width:100%;margin-bottom: 8px;">Overdraft</a><br>
                <a href="{{route('cost-labor.import',$project)}}" target="_blank"class="hvr-float-shadow btn btn-primary" style="width:100%; margin-bottom: 8px;">Labor Trend Analysis</a><br>
                <a href="/project/{{$project->id}}/issue-files" target="_blank" class="hvr-float-shadow btn btn-primary" style="width:100%;margin-bottom: 8px;">Issues</a><br>

                {{--<a href="{{route('show_issues.report',$project)}}" target="_blank"--}}
                   {{--class="hvr-float-shadow btn btn-primary"--}}
                   {{--style="width:100%;margin-bottom: 8px;">Issues</a><br>--}}
            </th>
            <th class="col-xs-6">
                <a href="{{route('cost_control.cost-summery',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Cost Summary</a><br>
                <a href="{{route('cost.standard_activity_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Standard Activity</a><br>
                <a href="{{route('cost.activity_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Activity</a><br>
                <a href="{{route('cost.resource_code_report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary"
                   style="width:100%; margin-bottom: 8px;">Resource Dictionary</a><br>
                {{--<a href="{{route('cost.dictionary',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"--}}
                   {{--style="width:100%;margin-bottom: 8px;">Resource Dictionary</a><br>--}}

                <a href="{{route('cost.variance',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary" style="width:100%;margin-bottom: 8px;">Variance Analysis</a><br>
                <a href="{{route('productivity-report.import',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary"
                   style="width:100%;margin-bottom: 8px;">Productivity
                </a>
        </tr>
        </thead>
    </table>
</section>

