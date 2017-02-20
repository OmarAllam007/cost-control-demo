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
        <a href="#col-{{$type['id']}}" data-toggle="collapse" style=" @if($tree_level ==0)
                color:white;
        @elseif($tree_level ==1)
                color: #000;
        @endif
                text-decoration: none;">{{$type['name']}}</a></p>
    <article id="col-{{$type['id']}}" class="tree--child collapse">
        @if(count($type['resources']))
            <ul class="list-unstyled">
                <li>

                    <article id="col-{{$type['id']}}" class="tree--child">

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
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(collect($type['resources'])->sortBy('name') as $keyResource=>$resource)
                                <tr>
                                    <td>{{$resource['name']}}</td>
                                    <td>{{number_format($resource['budget_cost']??0,2) }}</td>
                                    <td>{{number_format($resource['prev_cost']??0,2)}}</td>
                                    <td>{{number_format($resource['prev_allowabe']??0,2)}}</td>
                                    <td>{{number_format($resource['prev_variance']??0,2)}}</td>
                                    <td>{{number_format($resource['to_date_cost']??0,2)}}</td>
                                    <td>{{number_format($resource['to_date_allowable_cost']??0,2)}}</td>
                                    <td>{{number_format($resource['cost_var']??0,2)}}</td>
                                    <td>{{number_format($resource['remain_cost']??0,2)}}</td>
                                    <td>{{number_format($resource['completion_cost']??0,2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </article>
                </li>
            </ul>
        @endif
        @if (count($type['children']))
            <ul class="list-unstyled">
                @foreach($type['children'] as $child)
                    @include('reports.cost-control.resource_code._recursive_report', ['type' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif

    </article>

</li>