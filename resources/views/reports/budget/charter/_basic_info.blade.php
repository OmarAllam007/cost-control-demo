<article class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">Project Basic Information</h4>
    </div>


    <table class="table table-bordered table-condensed charter-table">
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
            <th>Project Plan Start Sate</th>
            <td>{{Carbon\Carbon::parse($project->project_start_date)->format('d M Y')}}</td>
        </tr>
        <tr>
            <th>Project Plan Finish Date</th>
            <td>{{Carbon\Carbon::parse($project->expected_finish_date)->format('d M Y')}}</td>
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
            <td>
                @if ($project->cost_owner)
                    Eng. {{$project->cost_owner->name}}
                @endif
            </td>
        </tr>

        {{-- Tender fields --}}
        <tr class="bg-blue-lightest">
            <th>Tender Direct Cost</th>
            <td>{{number_format($project->tender_direct_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lightest">
            <th>Tender Indirect Cost</th>
            <td>{{number_format($project->tender_indirect_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lightest">
            <th>Tender Risk and Escalation</th>
            <td>{{number_format($project->tender_risk, 2)}}</td>
        </tr>

        <tr class="bg-blue-lightest">
            <th>Tender Initial Profit</th>
            <td>{{number_format($project->tender_initial_profit, 2)}}</td>
        </tr>

        <tr class="bg-blue-lightest">
            <th>Tender Total Cost</th>
            <td>{{number_format($project->tender_total_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lightest">
            <th>Tender Initial Profitability Index</th>
            <td>{{number_format($project->tender_initial_profitability_index, 2)}}%</td>
        </tr>


        {{-- Budget fields --}}
        <tr class="bg-blue-lighter">
            <th>Direct Budget Cost</th>
            <td>{{number_format($project->direct_budget_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>General Requirement Budget Cost</th>
            <td>{{number_format($project->general_requirement_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>Management Reserve</th>
            <td>{{number_format($project->management_reserve_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>Total Budget Cost</th>
            <td>{{number_format($project->budget_cost, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>Site Work Cost / M<sup>2</sup></th>
            <td>{{number_format($project->sw_cost_per_m2, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>Building Cost / M<sup>2</sup></th>
            <td>{{number_format($project->building_cost_per_m2, 2)}}</td>
        </tr>

        <tr class="bg-blue-lighter">
            <th>Built up area cost / M<sup>2</sup></th>
            <td>{{number_format($project->total_built_cost_per_m2, 2)}}</td>
        </tr>
        <tr class="bg-blue-light">
            <th>Built up area price / M<sup>2</sup></th>
            <td>{{number_format($project->built_price_per_m2, 2)}}</td>
        </tr>

        {{-- Profit --}}
        <tr class="bg-blue-light">
            <th>EAC Contract Amount</th>
            <td>{{number_format($project->eac_contract_amount, 2)}}</td>
        </tr>

        <tr class="bg-blue-light">
            <th>Planned Profit Amount</th>
            <td>{{number_format($project->planned_profit_amount, 2)}}</td>
        </tr>

        <tr class="bg-blue-light">
            <th>Planned Profitability Index</th>
            <td>{{number_format($project->planned_profitability, 2)}}%</td>
        </tr>
        </tbody>
    </table>

</article>