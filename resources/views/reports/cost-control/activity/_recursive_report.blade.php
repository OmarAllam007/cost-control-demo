<li>
    <p class="
       @if($tree_level ==0)
            blue-first-level
         @elseif($tree_level ==1)
            blue-third-level
           @else
            blue-fourth-level
                @endif
            "
    >
        <label href="#col-{{$level['id']}}" data-toggle="collapse" style="text-decoration: none;">
            {{$level['name']}}
        </label>

    </p>

    <article id="col-{{$level['id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead>
            <tr>
                <td>Base Line</td>
                <td>Previous Cost</td>
                <td>To Date Cost</td>
                <td>Allawable (EV) Cost</td>
                <td>Remaining Cost</td>
                <td>To Date Variance</td>
                <td>At Compeletion Cost</td>
                <td>Cost Variance</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{number_format($level['data']['budget_cost'],2)}}</td>
                <td>{{number_format($level['data']['previous_cost'],2)}}</td>
                <td>{{number_format($level['data']['to_date_cost'],2)}}</td>
                <td>{{number_format($level['data']['allowable_ev_cost'],2)}}</td>
                <td>{{number_format($level['data']['remaining_cost'],2)}}</td>
                <td>{{number_format($level['data']['allowable_var'],2)}}</td>
                <td>{{number_format($level['data']['completion_cost'],2)}}</td>
                <td>{{number_format($level['data']['cost_var'],2)}}</td>
            </tr>
            </tbody>
        </table>
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                        @include('reports.cost-control.activity._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif
        @if ($level['activities'] && count($level['activities']))
            <ul class="list-unstyled">
                @foreach($level['activities'] as $activity)
                    <table class="table table-condensed">
                        <thead>
                        <tr class="output-cell">
                            <td>Activity</td>
                            <td>Base Line</td>
                            <td>Previous Cost</td>
                            <td>To Date Cost</td>
                            <td>Allawable (EV) Cost</td>
                            <td>Remaining Cost</td>
                            <td>To Date Variance</td>
                            <td>At Compeletion Cost</td>
                            <td>Cost Variance</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{$activity['activity']}}</td>
                            <td>{{number_format($activity['budget_cost'],2)}}</td>
                            <td>{{number_format($activity['previous_cost'],2)}}</td>
                            <td>{{number_format($activity['to_date_cost'],2)}}</td>
                            <td>{{number_format($activity['allowable_ev_cost'],2)}}</td>
                            <td>{{number_format($activity['remaining_cost'],2)}}</td>
                            <td>{{number_format($activity['allowable_var'],2)}}</td>
                            <td>{{number_format($activity['completion_cost'],2)}}</td>
                            <td>{{number_format($activity['cost_var'],2)}}</td>
                        </tr>
                        </tbody>
                    </table>
                @endforeach
            </ul>
        @endif


    </article>


</li>