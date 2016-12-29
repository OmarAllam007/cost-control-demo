{{--{{dd($type)}}--}}
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
                <td>Previous Cost</td>
                <td>Previous Var</td>
                <td>Previous Allowable</td>
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
                <td>{{number_format($type['data']['budget_cost'] ?? 0,2)}}</td>
                <td>{{number_format($type['data']['previous_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['previous_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['previous_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['to_date_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['allowable_ev_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['remaining_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['allowable_var'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['completion_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($type['data']['cost_var'] ?? 0 ,2)}}</td>
            </tr>
            </tbody>
        </table>
            @if ($type['resources'] && count($type['resources']))

                <table class="table table-condensed">
                    <thead>
                    <tr style="background: rgba(9, 0, 3, 0.13);font-weight: bold">
                        <td>Resource Name</td>
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
                    <tbody>
                    @foreach($type['resources'] as $resource)
                        <tr>
                            <td>{{$resource['name']??''}}</td>
                            <td>{{number_format($resource['data']['budget_cost'] ?? 0,2)}}</td>
                            <td>{{number_format($resource['data']['to_date_cost'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['previous_cost'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['allowable_ev_cost'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['remaining_cost'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['allowable_var'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['completion_cost'] ?? 0 ,2)}}</td>
                            <td>{{number_format($resource['data']['cost_var'] ?? 0 ,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            @if ($type['children'] && count($type['children']))

                <ul class="list-unstyled">
                    @foreach($type['children'] as $child)
                        @if($child['resources'] && count($child['resources']))
                        @include('reports.cost-control.dictionary._recursive_report', ['type' => $child, 'tree_level' => $tree_level + 1])
                        @endif
                    @endforeach
                </ul>
            @endif


        </article>


</li>