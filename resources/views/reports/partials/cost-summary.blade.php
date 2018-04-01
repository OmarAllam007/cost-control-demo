<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Cost Summary</h4>
    <section class="card-body">
    <table class="table table-condensed table-bordered cost-summary-table">
        <thead>
        <tr>
            <th class="text-center col-sm-2" rowspan="2"></th>
            <th class="text-center cost-summary-group">Budget</th>
            <th class="cost-summary-group">Previous</th>
            <th colspan="3" class="text-center cost-summary-group">To-Date</th>
            <th colspan="1" class="text-center cost-summary-group">Remaining</th>
            <th colspan="6" class="text-center cost-summary-group">At Completion</th>
        </tr>
        <tr>
            <th class="text-center col-xs-1 cost-summary-header">Base Line</th>
            <th class="text-center col-xs-1 cost-summary-header">Previous Cost</th>
            <th class="text-center col-xs-1 cost-summary-header">Allowable (EV) Cost</th>
            <th class="text-center col-xs-1 cost-summary-header">To Date Cost</th>
            <th class="text-center col-xs-1 cost-summary-header">To Date Cost Variance</th>
            <th class="text-center col-xs-1 cost-summary-header">Remaining Cost</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost (Optimistic)</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost (Most Likely)</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost (Pessimistic)</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost Var (Optimistic)</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost Var (Most Likely)</th>
            <th class="text-center col-xs-1 cost-summary-header">Completion Cost Var (Pessimistic)</th>
        </tr>

        </thead>
        <tbody>
        @foreach($costSummary as $type)
            <tr>
                <td class="cost-summary-side">{{$type->type}}</td>
                <td class="text-right">{{number_format($type->budget_cost,2) }}</td>
                <td class="text-right">{{number_format($type->previous_cost,2)}}</td>
                <td class="text-right">{{number_format($type->allowable_cost,2)}}</td>
                <td class="text-right">{{number_format($type->to_date_cost, 2)}}</td>
                <td class="text-right {{$type->to_date_var < 0? 'text-danger' : 'text-success'}}">{{number_format($type->to_date_var,2)}}</td>
                <td class="text-right">{{number_format($type->remaining_cost,2)}}</td>
                <td class="text-right">{{number_format($type->completion_cost_optimistic,2)}}</td>
                <td class="text-right">{{number_format($type->completion_cost_likely,2)}}</td>
                <td class="text-right">{{number_format($type->completion_cost_pessimistic,2)}}</td>
                <td class="text-right {{$type->completion_var_optimistic < 0? 'text-danger' : 'text-success'}}">{{number_format($type->completion_var_optimistic,2)}}</td>
                <td class="text-right {{$type->completion_var_likely < 0? 'text-danger' : 'text-success'}}">{{number_format($type->completion_var_likely,2)}}</td>
                <td class="text-right {{$type->completion_var_pessimistic < 0? 'text-danger' : 'text-success'}}">{{number_format($type->completion_var_pessimistic,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #F0FFF3">
            @php
            $to_date_var = $costSummary->sum('to_date_var');
            $completion_var_optimistic = $costSummary->sum('completion_var_optimistic');
            $completion_var_likely = $costSummary->sum('completion_var_likely');
            $completion_var_pessimistic = $costSummary->sum('completion_var_pessimistic');
            @endphp
            <td class="cost-summary-side">Total</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('budget_cost'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('previous_cost'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('allowable_cost'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('to_date_cost'),2) }}</td>
            <td class="text-right cost-summary-side {{$to_date_var < 0 ? 'text-danger' : 'text-success'}}">{{number_format($to_date_var,2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('remaining_cost'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('completion_cost_optimistic'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('completion_cost_likely'),2) }}</td>
            <td class="text-right cost-summary-side">{{number_format($costSummary->sum('completion_cost_pessimistic'),2) }}</td>
            <td class="text-right cost-summary-side {{$completion_var_optimistic < 0 ? 'text-danger' : 'text-success'}}">{{number_format($completion_var_optimistic,2) }}</td>
            <td class="text-right cost-summary-side {{$completion_var_likely < 0 ? 'text-danger' : 'text-success'}}">{{number_format($completion_var_likely,2) }}</td>
            <td class="text-right cost-summary-side {{$completion_var_pessimistic < 0 ? 'text-danger' : 'text-success'}}">{{number_format($completion_var_pessimistic,2) }}</td>
        </tr>
        </tfoot>
    </table>
    </section>
</section>
