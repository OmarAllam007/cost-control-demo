<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Cost Summary</h4>

    <div class="card-body">
        <table class="table table-bordered table-condensed dashboaad-cost-summary">
            <thead>
            <tr class="bg-primary">
                <th rowspan="2"></th>
                <th rowspan="2">Budget</th>
                <th rowspan="2">Allowable (EV) Cost</th>
                <th rowspan="2">To Date Cost</th>
                <th rowspan="2">To Date Cost Var +/-</th>
                <th rowspan="2">Remaining Cost</th>
                <th class="text-center">at Completion Cost</th>
                <th class="text-center">at Completion Cost Var +/-</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cost_summary as $type)
                <tr>
                    <td>{{ $type->type }}</td>
                    <td>{{number_format($type->budget_cost, 2)}}</td>

                    <td>{{number_format($type->allowable_cost, 2)}}</td>
                    <td>{{number_format($type->to_date_cost, 2)}}</td>
                    <td class="{{$type->to_date_var > 0? 'text-success' : 'text-danger'}}">{{number_format($type->to_date_var, 2)}}</td>

                    <td>{{number_format($type->remaining_cost, 2)}}</td>

                    <td>{{number_format($type->completion_cost, 2)}}</td>
                    <td class="{{$type->completion_cost_var> 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_cost_var, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="info">
                <th>Total</th>
                <th>{{number_format($cost_summary->sum('budget_cost'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('allowable_cost'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('to_date_cost'), 2) }}</th>
                <td class="{{$cost_summary->sum('to_date_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('to_date_var'), 2)}}</td>
                <th>{{number_format($cost_summary->sum('remaining_cost'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('completion_cost'), 2) }}</th>
                <td class="{{$cost_summary->sum('completion_cost_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_cost_var'), 2)}}</td>
            </tr>
            </tfoot>
        </table>

        <table class="table">
            <thead>
            <tr>
                <th class="borderless"></th>
                <th class="optimistic">Optimistic</th>
                <th class="most-liekly">Most Likely</th>
                <th class="pessimistic">Pessimistic</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <th class="bg-grey text-right">at Completion Cost</th>
                <td class="optimistic">{{number_format($completionValues[0], 2)}}</td>
                <td class="most-liekly">{{number_format($completionValues[1], 2)}}</td>
                <td class="pessimistic">{{number_format($completionValues[2], 2)}}</td>
            </tr>

            @php
                $budget_cost = $cost_summary->sum('budget_cost');
                $completion_var_optimistic = $budget_cost - $completionValues[0];
                $completion_var_likely = $budget_cost - $completionValues[1];
                $completion_var_pessimistic = $budget_cost - $completionValues[2];

            @endphp
            <tr>
                <th class="bg-grey text-right">at Completion Cost Var +/-</th>
                <td class="optimistic  {{$completion_var_optimistic > 0 ? 'text-red' : 'text-danger'}}">{{number_format($completion_var_optimistic, 2)}}</td>
                <td class="most-liekly {{$completion_var_likely > 0 ? 'text-red' : 'text-danger'}} ">{{number_format($completion_var_likely, 2)}}</td>
                <td class="pessimistic {{$completion_var_pessimistic > 0 ? 'text-success' : 'text-danger'}} ">{{number_format($completion_var_pessimistic, 2)}}</td>
            </tr>
            </tbody>

        </table>

    </div>
</section>