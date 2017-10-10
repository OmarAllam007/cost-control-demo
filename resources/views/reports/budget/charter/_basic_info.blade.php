<section class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">Project Basic Information</h4>
    </div>

    <table class="table table-bordered table-striped table-condensed">
        <tbody>
        <tr>
            <th class="col-sm-4">Project Name</th>
            <td>{{$project->name}}</td>
        </tr>
        <tr>
            <th>Project Client</th>
            <td>{{$project->client_name}}</td>
        </tr>
        <tr>
            <th>Project Consultant</th>
            <td>{{$project->consultant}}</td>
        </tr>
        <tr>
            <th>Project Location</th>
            <td>{{$project->project_location}}</td>
        </tr>
        <tr>
            <th>Project Type</th>
            <td>{{$project->project_type}}</td>
        </tr>
        <tr>
            <th>Project Duration</th>
            <td>{{$project->project_duration}}</td>
        </tr>
        <tr>
            <th>Project Plan Start Sate</th>
            <td>{{$project->project_start_date}}</td>
        </tr>
        <tr>
            <th>Project Plan Finish Date</th>
            <td>{{$project->expected_finished_date}}</td>
        </tr>
        <tr>
            <th>Contract Type</th>
            <td>{{$project->contract_type}}</td>
        </tr>
        <tr>
            <th>Project Selling Cost</th>
            <td>{{number_format($project->project_contract_signed_value, 2)}}</td>
        </tr>
        <tr>
            <th>Total Project Dry Cost</th>
            <td>{{number_format($project->dry_cost, 2)}}</td>
        </tr>
        <tr>
            <th>Project Overhead + GR</th>
            <td>{{number_format($project->overhead_and_gr, 2)}}</td>
        </tr>
        <tr>
            <th>Project Estimated Profit + Risk</th>
            <td>{{number_format($project->estimated_profit_and_risk, 2)}}</td>
        </tr>
        <tr class="info">
            <th>Project Total Budget</th>
            <td>{{number_format($total, 2)}}</td>
        </tr>
        <tr class="info">
            <th>Project Direct Cost Budget</th>
            <td>{{number_format($project->direct_cost, 2)}}</td>
        </tr>
        <tr class="info">
            <th>Project General Requirement Budget</th>
            <td>{{number_format($project->general_requirements, 2)}}</td>
        </tr>
        <tr class="info">
            <th>Management Reserve</th>
            <td>{{number_format($project->management_reserve, 2)}}</td>
        </tr>
        <tr class="info">
            <th>Project Estimated Profit After Budget</th>
            <td>{{number_format($project->profit, 2)}}</td>
        </tr>
        </tbody>
    </table>
</section>
