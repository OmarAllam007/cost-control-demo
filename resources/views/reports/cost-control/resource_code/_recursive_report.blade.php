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

                        <table class="table table-condensed" style="border-bottom: 2px solid black;">
                            <thead>
                            <tr style="border: 2px solid black">
                                <td></td>
                                <td colspan="3" style="border: 2px solid black;text-align: center">Budget</td>
                                <td colspan="6" style="border: 2px solid black;text-align: center">To-Date</td>
                                <td colspan="3" style="border: 2px solid black; text-align: center">Remaining</td>
                                <td colspan="4" style="border: 2px solid black;text-align: center">At Completion</td>
                                <td style="text-align: center"></td>
                            </tr>
                            <tr class="tbl-children-division">
                                <th style="border-left: 2px solid black;">Resource Name</th>
                                <th style="border-left: 2px solid black;">Unit Price</th>
                                <th>Quantity</th>
                                <th style="border-right: 2px solid black;">Cost</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                                <th>Allowable Cost</th>
                                <th>Quantity Var</th>
                                <th style="border-right: 2px solid black;">Cost variance</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th style="border-right: 2px solid black;">Cost</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                                <th style="border-right: 2px solid black;">Cost Variance</th>
                                <th style="border-right: 2px solid black;">P/W Index</th>


                            </tr>
                            </thead>
                            <tbody>
                            @foreach(collect($type['resources'])->sortBy('name') as $keyResource=>$resource)
                                <tr style="border-bottom: 1px solid lightgray;">
                                    <td style="border-left: 2px solid black;">{{$resource['name']}}</td>
                                    <td style="border-left: 2px solid black;">{{number_format($resource['unit_price']??0,2) }}</td>
                                    <td  style="background: #defdff;border-left:1px solid lightgray; border-right:1px solid  lightgray;"">{{number_format($resource['budget_unit']??0,2) }}</td>
                                    <td style="border-right: 2px solid black;">{{number_format($resource['budget_cost']??0,2) }}</td>
                                    <td>{{number_format($resource['to_date_unit_price']??0,2) }}</td>
                                    <td  style="background: #defdff;border-left:1px solid lightgray; border-right:1px solid  lightgray;">{{number_format($resource['to_date_qty']??0,2) }}</td>
                                    <td>{{number_format($resource['to_date_cost']??0,2) }}</td>
                                    <td>{{number_format($resource['allowable_ev_cost']??0,2) }}</td>
                                    <td>{{number_format($resource['quantity_var']??0,2) }}</td>
                                    <td style="border-right: 2px solid black; @if($resource['allowable_var']<0) color:red @endif">{{number_format($resource['allowable_var']??0,2) }}</td>
                                    <td>{{number_format($resource['remaining_unit_price']??0,2) }}</td>
                                    <td style="background: #defdff;border-left:1px solid lightgray; border-right:1px solid  lightgray;">{{number_format($resource['remaining_qty']??0,2) }}</td>
                                    <td style="border-right: 2px solid black;">{{number_format($resource['remaining_cost']??0,2) }}</td>
                                    <td>{{number_format($resource['completion_unit_price']??0,2) }}</td>
                                    <td style="background: #defdff;border-left:1px solid lightgray; border-right:1px solid  lightgray;">{{number_format($resource['completion_qty']??0,2) }}</td>
                                    <td>{{number_format($resource['completion_cost']??0,2) }}</td>
                                    <td style="border-right: 2px solid black; @if($resource['cost_var']<0) color:red @endif ">{{number_format($resource['cost_var']??0,2) }}</td>
                                    <td style="border-right: 2px solid black;">{{number_format($resource['pw_index']??0,2) }}</td>
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