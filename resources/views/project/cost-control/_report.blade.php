@can('cost_reports', $project)
<section id="CostControlReports" class="project-tab">

    @if (empty($skipButtons))
    <div class="form-group clearfix">
        <a class="btn btn-primary pull-right" href="{{route('communication.cost', $project)}}"><i
                    class="fa fa-send"></i> Send Reports</a>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <a href="{{route('cost_control.info',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Project Dashboard</a>
            </div>

            <div class="form-group">
                <a href="/project/{{$project->id}}/dashboard" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Graphical Report</a>
            </div>

            <div class="form-group">
                @if ($project->hasRollup())
                    <a href="#" disabled class="btn btn-primary btn-block">BOQ</a>
                @else
                    <a href="{{route('cost.boq_report',$project)}}" target="_blank"
                       class="hvr-float-shadow btn btn-primary btn-block">BOQ</a>
                @endif
            </div>

            <div class="form-group">
                @if ($project->hasRollup())
                    <a href="#" disabled class="btn btn-primary btn-block">Overdraft</a>
                @else
                    <a href="{{route('cost.overdraft',$project)}}" target="_blank"
                       class="hvr-float-shadow btn btn-primary btn-block">Overdraft</a>
                @endif
            </div>

            <div class="form-group">
                <a href="{{route('cost-labor.import',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Labor Trend Analysis</a>
            </div>

            <div class="form-group">
                <a href="{{route('project.concerns-report', $project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Issues &amp; Concerns Report</a>
            </div>

            <div class="form-group">
                <a href="{{route('threshold-report', $project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Threshold Report</a>
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <a href="{{route('cost_control.cost-summary',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Cost Summary</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.standard_activity_report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Standard Activity</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.activity_report',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-primary btn-block">Activity</a>
            </div>

            @if ($project->is_activity_rollup)
                <div class="form-group">
                    <a href="#" class="btn btn-primary btn-block" disabled>Resource Dictionary</a>
                </div>

                <div class="form-group">
                    <a href="#" class="btn btn-primary btn-block" disabled>Variance Analysis</a>
                </div>
            @else
                <div class="form-group">
                    <a href="{{route('cost.resource_code_report',$project)}}" target="_blank"
                       class="hvr-float-shadow btn btn-primary btn-block">Resource Dictionary</a>
                </div>

                <div class="form-group">
                    <a href="{{route('cost.variance',$project)}}" target="_blank"
                       class="hvr-float-shadow btn btn-primary btn-block">Variance Analysis</a>
                </div>
            @endif

            <div class="form-group">
                <a href="{{route('productivity-report.import',$project)}}" target="_blank"
                   class="hvr-float-shadow btn btn-block btn-primary btn-block">Productivity</a>
            </div>

            <div class="form-group">
                <a href="{{route('project.waste-index-report', $project)}}"
                   class="hvr-float-shadow btn btn-block btn-primary btn-block" target="_blank">Material Consumption Index</a>
            </div>

            <div class="form-group">
                <a href="{{route('project.productivity-index-report', $project)}}"
                   class="hvr-float-shadow btn btn-block btn-primary btn-block" target="_blank">Productivity
                    Index</a>
            </div>
        </div>
    </div>
</section>
@endcan