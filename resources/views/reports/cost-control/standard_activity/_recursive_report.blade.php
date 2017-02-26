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
        <a href="#{{$division['id']}}" data-toggle="collapse"
           @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$division['name']}}
        </a>


    </p>

    <article id="{{$division['id']}}" class="tree--child collapse">

        @if (collect($division['activities'])->sortBy('name') && count($division['activities']))
            @foreach($division['activities'] as $keyActivity=>$activity)
                <ul class="list-unstyled">
                    <li>
                        <p class="blue-fourth-level">
                            <a href="#{{$activity['id']}}" data-toggle="collapse">
                                {{$activity['name']}}
                            </a>

                        </p>
                        <article id="{{$activity['id']}}" class="tree--child collapse">
                            @if(count($activity['resources']))
                                <ul class="list-unstyled">
                                    <li>
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr class="tbl-children-division">
                                                <th>Resource Name</th>
                                                <th>Base Line</th>
                                                <th>Previous Cost</th>
                                                <th>Previous allowable</th>
                                                <th>Previous Variance</th>
                                                <th>To Date Cost</th>
                                                <th>Allowable (EV) Cost</th>
                                                <th>To Date Variance</th>
                                                <th>Remaining Cost</th>
                                                <th>At Compeletion Cost</th>
                                                <th>Cost Variance</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(collect($activity['resources'])->sortBy('name') as $keyResource=>$resource)
                                                <tr>
                                                    <td>{{$resource['name']}}</td>
                                                    <td>{{number_format($resource['budget_cost']??0,2) }}</td>
                                                    <td>{{number_format($resource['prev_cost']??0,2)}}</td>
                                                    <td>{{number_format($resource['prev_allowabe']??0,2)}}</td>
                                                    <td>{{number_format($resource['prev_variance']??0,2)}}</td>
                                                    <td>{{number_format($resource['to_data_cost']?? 0)}}</td>
                                                    <td>{{number_format($resource['to_date_allowable_cost']??0,2)}}</td>
                                                    <td>{{number_format($resource['allowable_var']??0,2)}}</td>
                                                    <td>{{number_format($resource['remain_cost']??0,2)}}</td>
                                                    <td>{{number_format($resource['completion_cost']??0,2)}}</td>
                                                    <td>{{number_format($resource['cost_var']??0,2)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            @endif
                        </article>
                    </li>
                </ul>
            @endforeach
        @endif
        @if (isset($division['children']) && count($division['children']))
            <ul class="list-unstyled">
                @foreach($division['children'] as $child)
                    @if(count($child['activities']))
                        @include('reports.cost-control.standard_activity._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                    @endif
                @endforeach
            </ul>
        @endif

    </article>
</li>
