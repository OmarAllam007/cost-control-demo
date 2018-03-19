<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Cost Summary</h4>

    <div class="card-body">
        <table class="table table-bordered table-condensed dashboaad-cost-summary">
        <thead>
            <tr class="bg-primary">
                <th></th>
                <th>Budget</th>
                <th>Allowable (EV) Cost</th>
                <th>To Date Cost</th>
                <th>Todate Cost Var +/-</th>
                <th>Remaining Cost</th>
                <th>at Completion Cost</th>
                <th>at Completion Cost Var +/-</th>
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
                <td class="{{$type->completion_cost_var > 0? 'text-success' : 'text-danger'}}">{{number_format($type->completion_cost_var, 2)}}</td>
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
    </div>
</section>