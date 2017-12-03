<section class="card">
    <h3 class="card-title">Cost Summary</h3>

    <div class="card-body">
        <table class="table table-bordered table-condensed dashboaad-cost-summary">
        <thead>
            <tr class="bg-primary">
                <th rowspan="2">Resource Type</th>
                <th>Budget</th>
                <th colspan="3">Previous</th>
                <th colspan="3">To Date</th>
                <th>Remaining</th>
                <th colspan="2">At Completion</th>
            </tr>
            <tr class="info">
                <th>Baseline</th>
                <th>Previous Cost</th>
                <th>Previous (EV) Allowable</th>
                <th>Previous Cost Var +/-</th>
                <th>To Date Cost</th>
                <th>Allowable (EV) Cost</th>
                <th>Todate Cost Var +/-</th>
                <th>Remaining Cost</th>
                <th>at Completion Cost</th>
                <th>at Completion Cost Var +/-</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cost_summary as $type)
            <tr>
                <td>{{$type->resource_type}}</td>
                <td>{{number_format($type->budget_cost, 2)}}</td>

                <td>{{number_format($type->previous_cost, 2)}}</td>
                <td>{{number_format($type->previous_allowable, 2)}}</td>
                <td class="{{$type->previous_var > 0? 'text-success' : 'text-danger'}}">{{number_format($type->previous_var, 2)}}</td>

                <td>{{number_format($type->to_date_cost, 2)}}</td>
                <td>{{number_format($type->to_date_allowable, 2)}}</td>
                <td class="{{$type->to_date_var > 0? 'text-success' : 'text-danger'}}">{{number_format($type->to_date_var, 2)}}</td>

                <td>{{number_format($type->remaining_cost, 2)}}</td>

                <td>{{number_format($type->completion_cost, 2)}}</td>
                <td class="{{$type->completion_var > 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_var, 2)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="info">
                <th>Total</th>
                <th>{{number_format($cost_summary->sum('budget_cost')) }}</th>
                <th>{{number_format($cost_summary->sum('previous_cost')) }}</th>
                <th>{{number_format($cost_summary->sum('previous_allowable')) }}</th>
                <td class="{{$cost_summary->sum('previous_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('previous_var'), 2)}}</td>
                <th>{{number_format($cost_summary->sum('to_date_cost')) }}</th>
                <th>{{number_format($cost_summary->sum('to_date_allowable')) }}</th>
                <td class="{{$cost_summary->sum('to_date_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('to_date_var'), 2)}}</td>
                <th>{{number_format($cost_summary->sum('remaining_cost')) }}</th>
                <th>{{number_format($cost_summary->sum('completion_cost')) }}</th>
                <td class="{{$cost_summary->sum('completion_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_var'), 2)}}</td>
            </tr>
        </tfoot>
        </table>
    </div>
</section>