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
                 ;text-decoration: none;">{{$tKey}}</a></p>

    <article id="col-{{$type['id']}}" class="tree--child collapse">
        @if(count($type['disciplines']))
            <ul class="list-unstyled tree">
                @foreach(collect($type['disciplines'])->sortBy('name') as $key=>$discpline)
                    <li>
                        <p class="blue-fourth-level">
                            <a href="#{{strtolower(str_replace([' ','.'],'',$tKey))}}{{$key}}" data-toggle="collapse"
                               style="text-decoration: none;">{{$key}}</a></p>

                        <article id="{{strtolower(str_replace([' ','.'],'',$tKey))}}{{$key}}" class="tree--child collapse">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <td colspan="5" class="blue-second-level"
                                            style="text-align: center;border: solid #000000">Unit Price
                                            Analysis
                                        </td>
                                        <td colspan="9" class="blue-second-level"
                                            style="text-align: center;border: solid #000000">Quantity
                                            Analysis
                                        </td>
                                    </tr>
                                    <tr style="text-align: center;border: solid #000000">
                                        <td class="thirdGroup">Resource Name</td>
                                        <td class="firstGroup">Budget Cost</td>
                                        <td class="firstGroup">Price / Unit</td>
                                        <td class="firstGroup">Current Price / Unit</td>
                                        <td class="firstGroup">Todate Price / Unit +/-</td>
                                        <td class="secondGroup" style="text-align: center;border-left: solid #000000">Todate Cost Variance</td>
                                        <td class="secondGroup">at Variance Cost Variance due to unit price</td>
                                        <td class="secondGroup">at Completion Cost Variance due to unit price</td>
                                        <td class="secondGroup">Budget Unit</td>
                                        <td class="secondGroup">Todate Qty</td>
                                        <td class="secondGroup">Allowable Quantity</td>
                                        <td class="secondGroup">Qty Variance</td>
                                        <td class="secondGroup">at Variance Cost Variance due to Quantity</td>
                                        <td class="secondGroup">at Completion Cost Variance due to Quantity</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(collect($discpline['resources'])->sortBy('resource_name') as $rKey=>$resource)
                                        @if($resource['resource_name'])
                                            <tr>
                                                <td class="cell-borders">{{$resource['resource_name']}}</td>
                                                <td class="cell-borders">{{number_format($resource['budget_cost'],2)  ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['unit_price'],2)  ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['curr_unit_price'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['to_date_unit_price'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['unit_price_var'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['cost_variance_to_date_due_unit_price'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['cost_variance_completion_due_unit_price'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['budget_unit'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['to_date_qty'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['allowable_qty'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['qty_var'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['cost_variance_to_date_due_qty'],2) ??0}}</td>
                                                <td class="cell-borders">{{number_format($resource['cost_variance_completion_due_qty'],2) ??0}}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>

        @endif


    </article>


</li>