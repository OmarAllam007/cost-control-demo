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
        <label href="#col-{{$type['resource_type_id']}}" data-toggle="collapse" style="text-decoration: none;">{{\App\ResourceType::find($type['resource_type_id'])->name}}</label></p>
    <article id="col-{{$type['resource_type_id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead>
            <tr>
                <td colspan="4" class="blue-second-level" style="text-align: center;border: solid #000000">Unit Price Analysis</td>
                <td colspan="4" class="blue-second-level" style="text-align: center;border: solid #000000">Quantity Analysis</td>
                <td colspan="2" class="blue-second-level" style="text-align: center;border: solid #000000">Effect of variances</td>
            </tr>
            <tr class="output-cell">
                <td>Price / Unit</td>
                <td>Todate Price / Unit</td>
                <td>Price / Unit +/-</td>
                <td>Todate Cost Variance</td>
                <td>Allowable Unit</td><!--required-->
                <td>Todate Qty</td>
                <td>Quantity + / -</td>
                <td>Todate Cost Variance</td>
                <td>at Completion Cost Variance due to Quantity</td>
                <td> at Completion Cost Variance due to unit price</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{number_format($type['unit_price'],2)}}</td>
                <td>{{number_format($type['to_date_unit_price'],2)}}</td>
                <td>{{number_format($type['unit_price_var'],2)}}</td>
                <td>{{number_format($type['cost_var'],2)}}</td>
                <td></td>
                <td>{{number_format($type['to_date_qty'],2)}}</td>
                <td>{{number_format($type['qty_var'],2)}}</td>
                <td>{{number_format($type['cost_var'],2)}}</td>
                <td>{{number_format($type['cost_variance_completion_due_qty'],2)}}</td>
                <td>{{number_format($type['cost_variance_completion_due_unit_price'],2)}}</td>
            </tr>
            </tbody>
        </table>
        @if ($type['cost_accounts'] && count($type['cost_accounts']))
            <ul class="list-unstyled">
                @foreach($type['cost_accounts'] as $key=>$cost_account)
                    <li>
                        <p class="blue-second-level"
                        ><label href="#{{str_replace(['-','.'],'',$key)}}" data-toggle="collapse" style="text-decoration: none;">{{$cost_account['cost_account']}}</label></p>
                        <article id="{{str_replace(['-','.'],'',$key)}}" class="tree--child collapse">
                            <table class="table table-condensed">
                                <thead>
                                <tr>

                                    <td colspan="5" class="blue-third-level" style="text-align: center;border: solid #000000">Unit Price Analysis</td>
                                    <td colspan="4" class="blue-third-level" style="text-align: center;border: solid #000000">Quantity Analysis</td>
                                    <td colspan="2" class="blue-third-level" style="text-align: center;border: solid #000000">Effect of variances</td>
                                </tr>
                                <tr class="output-cell">
                                    <td>Cost Account</td>
                                    <td>Price / Unit</td>
                                    <td>Todate Price / Unit</td>
                                    <td>Price / Unit +/-</td>
                                    <td>Todate Cost Variance</td>
                                    <td>Allowable Unit</td><!--required-->
                                    <td>Todate Qty</td>
                                    <td>Quantity + / -</td>
                                    <td>Todate Cost Variance</td>
                                    <td>at Completion Cost Variance due to Quantity</td>
                                    <td> at Completion Cost Variance due to unit price</td>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td>{{$cost_account['cost_account']}}</td>
                                    <td>{{number_format($cost_account['unit_price'],2)}}</td>
                                    <td>{{number_format($cost_account['to_date_unit_price'],2)}}</td>
                                    <td>{{number_format($cost_account['unit_price_var'],2)}}</td>
                                    <td>{{number_format($cost_account['cost_var'],2)}}</td>
                                    <td></td>
                                    <td>{{number_format($cost_account['to_date_qty'],2)}}</td>
                                    <td>{{number_format($cost_account['qty_var'],2)}}</td>
                                    <td>{{number_format($cost_account['cost_var'],2)}}</td>
                                    <td>{{number_format($cost_account['cost_variance_completion_due_qty'],2)}}</td>
                                    <td>{{$cost_account['cost_variance_completion_due_unit_price']}}</td>
                                </tr>
                                </tbody>
                            </table>
                            @if ($cost_account['resources'] && count($cost_account['resources']))
                                <ul class="list-unstyled">
                                    @foreach($cost_account['resources'] as $rKey=>$resource)
                                        <li>
                                            <p class="blue-third-level"
                                            ><label href="#{{str_replace(['-','.'],'',$key).''.$rKey}}" data-toggle="collapse" style="text-decoration: none;">{{\App\Resources::find
                                            ($resource['resource_id'])->name}}</label></p>

                                            <article id="{{str_replace(['-','.'],'',$key).''.$rKey}}" class="tree--child collapse">
                                                <table class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <td colspan="5" class="blue-third-level" style="text-align: center;border: solid #000000">Unit Price Analysis</td>
                                                        <td colspan="4" class="blue-third-level" style="text-align: center;border: solid #000000">Quantity Analysis</td>
                                                        <td colspan="2" class="blue-third-level" style="text-align: center;border: solid #000000">Effect of variances</td>
                                                    </tr>
                                                    <tr class="output-cell">
                                                        <td>Price / Unit</td>
                                                        <td>Todate Price / Unit</td>
                                                        <td>Price / Unit +/-</td>
                                                        <td>Todate Cost Variance</td>
                                                        <td>Allowable Unit</td><!--required-->
                                                        <td>Todate Qty</td>
                                                        <td>Quantity + / -</td>
                                                        <td>Todate Cost Variance</td>
                                                        <td>at Completion Cost Variance due to Quantity</td>
                                                        <td> at Completion Cost Variance due to unit price</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr>
                                                        <td>{{number_format($resource['unit_price'],2)}}</td>
                                                        <td>{{number_format($resource['to_date_unit_price'],2)}}</td>
                                                        <td>{{number_format($resource['unit_price_var'],2)}}</td>
                                                        <td>{{number_format($resource['cost_var'],2)}}</td>
                                                        <td></td>
                                                        <td>{{number_format($resource['to_date_qty'],2)}}</td>
                                                        <td>{{number_format($resource['qty_var'],2)}}</td>
                                                        <td>{{number_format($resource['cost_var'],2)}}</td>
                                                        <td>{{number_format($resource['cost_variance_completion_due_qty'],2)}}</td>
                                                        <td>{{$resource['cost_variance_completion_due_unit_price']}}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </article>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif

    </article>


</li>