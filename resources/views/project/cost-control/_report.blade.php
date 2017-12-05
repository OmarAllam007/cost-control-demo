<section id="CostControlReports" class="project-tab">
    <div class="row">

        <div class="col-xs-6">
            <div class="form-group">
                <a href="{{route('cost_control.info',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Project Information</a>
            </div>

            <div class="form-group">
                <a href="/project/{{$project->id}}/dashboard" target="_blank" class="hvr-float-shadow btn btn-primary btn-block" >Dashboard</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.boq_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block" >BOQ</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.overdraft',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block" >Overdraft</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost-labor.import',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Labor Trend Analysis</a>
            </div>

            <div class="form-group">
                <a href="/project/{{$project->id}}/issue-files" target="_blank" class="hvr-float-shadow btn btn-primary btn-block" >Issues</a>
            </div>

            <div class="form-group">
                <a href="{{route('threshold-report', $project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block" >Threshold Report</a>
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <a href="{{route('cost_control.cost-summary',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Cost Summary</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.standard_activity_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Standard Activity</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.activity_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Activity</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.resource_code_report',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Resource Dictionary</a>
            </div>

            <div class="form-group">
                <a href="{{route('cost.variance',$project)}}" target="_blank" class="hvr-float-shadow btn btn-primary btn-block">Variance Analysis</a>
            </div>

            <div class="form-group">
                <a href="{{route('productivity-report.import',$project)}}" target="_blank" class="hvr-float-shadow btn btn-block btn-primary btn-block">Productivity</a>
            </div>

            <div class="form-group">
                <a href="{{route('project.waste-index-report', $project)}}" class="hvr-float-shadow btn btn-block btn-primary btn-block" target="_blank">Waste Index</a>
            </div>

            <div class="form-group">
                <a href="{{route('project.productivity-index-report', $project)}}" class="hvr-float-shadow btn btn-block btn-primary btn-block" target="_blank">Productivity Index</a>
            </div>

        </div>

    </div>
</section>

