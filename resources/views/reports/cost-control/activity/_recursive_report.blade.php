<li>
    <p class="
       @if($tree_level ==0)
            blue-first-level
         @elseif($tree_level ==1)
            blue-second-level
           @else
            blue-fourth-level
                @endif
            "
    >
        <a href="#col-{{$level['id']}}" data-toggle="collapse" style="@if($tree_level>1) color: black;  @else color: white; @endif text-decoration: none !important;">
            {{$level['name']}}
        </a>

    </p>

    <article id="col-{{$level['id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead  style="background:#95DAC2;color: #000; border-bottom: solid black">
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
                <td>{{number_format($level['data']['prev_cost'],2)}}</td>
                <td>{{number_format($level['data']['to_date_cost'],2)}}</td>
                <td>{{number_format($level['data']['allowable_cost'],2)}}</td>
                <td>{{number_format($level['data']['remain_cost'],2)}}</td>
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
                        @foreach(collect($level['activities'])->sortBy('activity_name') as $activity)
                            <tr>
                            <td>{{$activity['activity_name']}}</td>
                            <td>{{number_format($activity['budget_cost'],2)}}</td>
                            <td>{{number_format($activity['prev_cost'],2)}}</td>
                            <td>{{number_format($activity['to_date_cost'],2)}}</td>
                            <td>{{number_format($activity['allowable_cost'],2)}}</td>
                            <td>{{number_format($activity['remain_cost'],2)}}</td>
                            <td>{{number_format($activity['allowable_var'],2)}}</td>
                            <td>{{number_format($activity['completion_cost'],2)}}</td>
                            <td>{{number_format($activity['cost_var'],2)}}</td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
            </ul>
        @endif


    </article>


</li>