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
                <th class="text-center" colspan="3">Completion Cost</th>
                <th class="text-center" colspan="3">Completion Cost Var +/-</th>
            </tr>
            <tr class="bg-primary">
                <th>Optimistic</th>
                <th>Most Likely</th>
                <th>Pessimistic</th>

                <th>Optimistic</th>
                <th>Most Likely</th>
                <th>Pessimistic</th>
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

                <td>{{number_format($type->completion_cost_optimistic, 2)}}</td>
                <td>{{number_format($type->completion_cost_likely, 2)}}</td>
                <td>{{number_format($type->completion_cost_pessimistic, 2)}}</td>

                <td class="{{$type->completion_cost_var_optimistic > 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_cost_var_optimistic, 2)}}</td>
                <td class="{{$type->completion_cost_var_likely > 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_cost_var_likely, 2)}}</td>
                <td class="{{$type->completion_cost_var_pessimistic > 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_cost_var_pessimistic, 2)}}</td>
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
                <th>{{number_format($cost_summary->sum('completion_cost_optimistic'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('completion_cost_likely'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('completion_cost_pessimistic'), 2) }}</th>
                <td class="{{$cost_summary->sum('completion_cost_var_optimistic') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_cost_var_optimistic'), 2)}}</td>
                <td class="{{$cost_summary->sum('completion_cost_var_likely') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_cost_var_likely'), 2)}}</td>
                <td class="{{$cost_summary->sum('completion_cost_var_pessimistic') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_cost_var_pessimistic'), 2)}}</td>
            </tr>
        </tfoot>
        </table>
    </div>
</section>