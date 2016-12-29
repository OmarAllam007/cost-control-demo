{{dd($tree)}}
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
        <label href="#col-{{$level['id']}}" data-toggle="collapse" style="text-decoration: none;">{{$level['name']}}</label></p>

    <article id="col-{{$level['id']}}" class="tree--child collapse">
        @if($level['activities']['activity_id']!=0)
        <table class="table table-condensed">
            <thead>
            <tr class="output-cell">
                <td>Base Line</td>
                <td>To Date Cost</td>
                <td>Previous Cost</td>
                <td>Allawable (EV) Cost</td>
                <td>Remaining Cost</td>
                <td>To Date Variance</td>
                <td>At Compeletion Cost</td>
                <td>Cost Variance</td>
            </tr>
            </thead>
            <tbody >
            <tr>
                <td>{{number_format($level['activities']['budget_cost'],2)}}</td>
                <td>{{number_format($level['activities']['to_date_cost'],2)}}</td>
                <td>{{number_format($level['activities']['previous_cost'],2)}}</td>
                <td>{{number_format($level['activities']['allowable_ev_cost'],2)}}</td>
                <td>{{number_format($level['activities']['remaining_cost'],2)}}</td>
                <td>{{number_format($level['activities']['allowable_var'],2)}}</td>
                <td>{{number_format($level['activities']['completion_cost'],2)}}</td>
                <td>{{number_format($level['activities']['cost_var'],2)}}</td>
            </tr>
            </tbody>
        </table>
        @endif
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.cost-control.activity._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif



    </article>


</li>