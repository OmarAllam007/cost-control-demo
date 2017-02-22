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
        <a href="#col-{{$type['id']}}" data-toggle="collapse" style="
color:@if($tree_level ==0) white @elseif($tree_level ==1) black

        @else
                black
        @endif
                 ;text-decoration: none;">{{$type['name']}}</a></p>

    <article id="col-{{$type['id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead>
            <tr>
                <td colspan="4" class="blue-second-level" style="text-align: center;border: solid #000000">Unit Price
                    Analysis
                </td>
                <td colspan="9" class="blue-second-level" style="text-align: center;border: solid #000000">Quantity
                    Analysis
                </td>
            </tr>
            <tr class="output-cell">
                <td>Price / Unit</td>
                <td>Current Price / Unit</td>
                <td>Todate Price / Unit +/-</td>
                <td>Todate Cost Variance</td>
                <td>at Variance Cost Variance due to unit price</td>
                <td>at Completion Cost Variance due to unit price</td>
                <td>Budget Unit</td>
                <td>Todate Qty</td>
                <td>Allowable Quantity</td>
                <td>Qty Variance</td>
                <td>at Variance Cost Variance due to Quantity</td>
                <td>at Completion Cost Variance due to Quantity</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{number_format($type['unit_price'],2)  ??0}}</td>
                <td>{{number_format($type['curr_unit_price'],2) ??0}}</td>
                <td>{{number_format($type['to_date_unit_price'],2) ??0}}</td>
                <td>{{number_format($type['unit_price_var'],2) ??0}}</td>
                <td>{{number_format($type['cost_variance_to_date_due_unit_price'],2) ??0}}</td>
                <td>{{number_format($type['cost_variance_completion_due_unit_price'],2) ??0}}</td>
                <td>{{number_format($type['budget_unit'],2) ??0}}</td>
                <td>{{number_format($type['to_date_qty'],2) ??0}}</td>
                <td>{{number_format($type['allowable_qty'],2) ??0}}</td>
                <td>{{number_format($type['qty_var'],2) ??0}}</td>
                <td>{{number_format($type['cost_variance_to_date_due_qty'],2) ??0}}</td>
                <td>{{number_format($type['cost_variance_completion_due_qty'],2) ??0}}</td>
            </tr>
            </tbody>
        </table>
        @if(count($type['discpline']))
            <ul class="list-unstyled tree">
                @foreach($type['discpline'] as $key=>$discpline)
                    <li>
                        <p class="blue-fourth-level">
                            <a href="#{{strtolower(str_replace([' ','.'],'',$key))}}{{$tKey}}" data-toggle="collapse"
                               style="text-decoration: none;">{{$key}}</a></p>

                        <article id="{{strtolower(str_replace([' ','.'],'',$key))}}{{$tKey}}"
                                 class="tree--child collapse">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <td colspan="4" class="blue-second-level"
                                        style="text-align: center;border: solid #000000">Unit Price
                                        Analysis
                                    </td>
                                    <td colspan="9" class="blue-second-level"
                                        style="text-align: center;border: solid #000000">Quantity
                                        Analysis
                                    </td>
                                </tr>
                                <tr class="output-cell">
                                    <td>Resource Name</td>
                                    <td>Price / Unit</td>
                                    <td>Current Price / Unit</td>
                                    <td>Todate Price / Unit +/-</td>
                                    <td>Todate Cost Variance</td>
                                    <td>at Variance Cost Variance due to unit price</td>
                                    <td>at Completion Cost Variance due to unit price</td>
                                    <td>Budget Unit</td>
                                    <td>Todate Qty</td>
                                    <td>Allowable Quantity</td>
                                    <td>Qty Variance</td>
                                    <td>at Variance Cost Variance due to Quantity</td>
                                    <td>at Completion Cost Variance due to Quantity</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(collect($discpline['resources'])->sortBy('resource_name') as $rKey=>$resource)
                                    @if($resource['resource_name'])
                                        <tr>
                                            <td>{{$resource['resource_name']}}</td>
                                            <td>{{number_format($resource['unit_price'],2)  ??0}}</td>
                                            <td>{{number_format($resource['curr_unit_price'],2) ??0}}</td>
                                            <td>{{number_format($resource['to_date_unit_price'],2) ??0}}</td>
                                            <td>{{number_format($resource['unit_price_var'],2) ??0}}</td>
                                            <td>{{number_format($resource['cost_variance_to_date_due_unit_price'],2) ??0}}</td>
                                            <td>{{number_format($resource['cost_variance_completion_due_unit_price'],2) ??0}}</td>
                                            <td>{{number_format($resource['budget_unit'],2) ??0}}</td>
                                            <td>{{number_format($resource['to_date_qty'],2) ??0}}</td>
                                            <td>{{number_format($resource['allowable_qty'],2) ??0}}</td>
                                            <td>{{number_format($resource['qty_var'],2) ??0}}</td>
                                            <td>{{number_format($resource['cost_variance_to_date_due_qty'],2) ??0}}</td>
                                            <td>{{number_format($resource['cost_variance_completion_due_qty'],2) ??0}}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                            </table>
                        </article>
                    </li>
                @endforeach
            </ul>

        @endif
        @if (count($type['children']))
            <ul class="list-unstyled">
                @foreach($type['children'] as $child)
                    @include('reports.cost-control.variance_analysis._recursive_report', ['type' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif

    </article>


</li>