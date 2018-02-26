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
            <th class="col-sm-4">Project Code</th>
            <td>{{$project->project_code}}</td>
        </tr>
        <tr>
            <th>Project Type</th>
            <td>{{$project->project_type}}</td>
        </tr>
        <tr>
            <th>Contract Type</th>
            <td>{{$project->contract_type}}</td>
        </tr>
        <tr>
            <th>Client Name</th>
            <td>{{$project->client_name}}</td>
        </tr>
        <tr>
            <th>Consultant Name</th>
            <td>{{$project->consultant}}</td>
        </tr>
        <tr>
            <th>Project Location</th>
            <td>{{$project->project_location}}</td>
        </tr>

        <tr>
            <th>Original Project Duration ( Days )</th>
            <td>{{$project->project_duration}}</td>
        </tr>

        <tr>
            <th>Planned Start Sate</th>
            <td>{{$project->project_start_date}}</td>
        </tr>

        <tr>
            <th>Planned Finish Date</th>
            <td>{{$project->expected_finish_date}}</td>
        </tr>

        <tr>
            <th>Original Signed Contract Value</th>
            <td>{{number_format($project->project_contract_signed_value ?: 0, 2)}}</td>
        </tr>

        <tr>
            <th>Budget Control Owner</th>
            <td>Eng. {{$project->owner->name}}</td>
        </tr>

        <tr>
            <th>Cost Control Owner</th>
            <td>Eng. {{$project->cost_owner->name}}</td>
        </tr>

        {{-- Tender fields --}}
        <tr>
            <th>Tender Direct Cost</th>
            <td>{{number_format($project->tender_direct_cost, 2)}}</td>
        </tr>

        <tr>
            <th>Tender Indirect Cost</th>
            <td>{{number_format($project->tender_indirect_cost, 2)}}</td>
        </tr>

        <tr>
            <th>Tender Risk and Escalation</th>
            <td>{{number_format($project->tender_risk, 2)}}</td>
        </tr>

        <tr>
            <th>Tender Initial Profit</th>
            <td>{{number_format($project->tender_initial_profit, 2)}}</td>
        </tr>

        <tr>
            <th>Tender Total Cost</th>
            <td>{{number_format($project->tender_total_cost, 2)}}</td>
        </tr>

        <tr>
            <th>Tender Initial Profitability Index</th>
            <td>{{number_format($project->tender_initial_profitability_index, 2)}}%</td>
        </tr>


        {{-- Budget fields --}}
        <tr class="info">
            <th>Direct Budget Cost</th>
            <td>{{number_format($project->direct_budget_cost, 2)}}</td>
        </tr>

        <tr class="info">
            <th>General Requirement Budget Cost</th>
            <td>{{number_format($project->general_requirement_cost, 2)}}</td>
        </tr>

        <tr class="info">
            <th>Management Reserve</th>
            <td>{{number_format($project->management_reserve_cost, 2)}}</td>
        </tr>

        <tr class="info">
            <th>Total Budget Cost</th>
            <td>{{number_format($project->budget_cost, 2)}}</td>
        </tr>

        {{-- Profit --}}
        <tr>
            <th>EAC Contract Amount</th>
            <td>{{number_format($project->eac_contract_amount, 2)}}</td>
        </tr>

        <tr>
            <th>Planned Profit Amount</th>
            <td>{{number_format($project->planned_profit_amount, 2)}}</td>
        </tr>

        <tr>
            <th>Planned Profitability Index</th>
            <td>{{number_format($project->planned_profitability, 2)}}%</td>
        </tr>
        </tbody>
    </table>
</section>
