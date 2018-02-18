<section class="card">
    <h3 class="card-title dark-cyan">Cost Summary</h3>

    <div class="card-body">
        <table class="table table-bordered table-condensed dashboaad-cost-summary">
        <thead>
            <tr class="bg-primary">
                <th>Project</th>
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
            @foreach($cost_summary as $project)
            <tr>
                <td>{{ $project->project_name }}</td>
                <td>{{number_format($project->budget_cost, 2)}}</td>

                <td>{{number_format($project->allowable_cost, 2)}}</td>
                <td>{{number_format($project->to_date_cost, 2)}}</td>
                <td class="{{$project->to_date_var > 0? 'text-success' : 'text-danger'}}">{{number_format($project->to_date_var, 2)}}</td>

                <td>{{number_format($project->remaining_cost, 2)}}</td>

                <td>{{number_format($project->completion_cost, 2)}}</td>
                <td class="{{$project->completion_var > 0? 'text-success' : 'text-danger'}}">{{number_format($project->completion_var, 2)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="info">
                <th>Total</th>
                <th>{{number_format($cost_summary->sum('budget_cost'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('to_date_allowable'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('to_date_cost'), 2) }}</th>
                <td class="{{$cost_summary->sum('to_date_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('to_date_var'), 2)}}</td>
                <th>{{number_format($cost_summary->sum('remaining_cost'), 2) }}</th>
                <th>{{number_format($cost_summary->sum('completion_cost'), 2) }}</th>
                <td class="{{$cost_summary->sum('completion_var') > 0? 'text-success' : 'text-danger'}}">{{number_format($cost_summary->sum('completion_var'), 2)}}</td>
            </tr>
        </tfoot>
        </table>
    </div>
</section>