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
        <label href="#col-{{$type['id']}}" data-toggle="collapse" style="text-decoration: none;">{{$type['name']}}</label></p>
    <article id="col-{{$type['id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead>
            <tr class="output-cell">
                <td>Base Line</td>
                <td>Toe Date Cost</td>
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
                <td>{{number_format($type['data']['budget_cost'],2)}}</td>
                <td>{{number_format($type['data']['to_date_cost'],2)}}</td>
                <td>{{number_format($type['data']['previous_cost'],2)}}</td>
                <td>{{number_format($type['data']['allowable_ev_cost'],2)}}</td>
                <td>{{number_format($type['data']['remaining_cost'],2)}}</td>
                <td>{{number_format($type['data']['allowable_var'],2)}}</td>
                <td>{{number_format($type['data']['completion_cost'],2)}}</td>
                <td>{{number_format($type['data']['cost_var'],2)}}</td>
            </tr>
            </tbody>
        </table>
        @if ($type['children'] && count($type['children']))
            <ul class="list-unstyled">
                @foreach($type['children'] as $child)
                    @include('reports.cost-control.boq-report._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif



    </article>


</li>