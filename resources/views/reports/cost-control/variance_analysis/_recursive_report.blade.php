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
                <td>Allowable Unit</td>
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
                <td>{{number_format($type['allowable_qty'],2)}}</td>
                <td>{{number_format($type['to_date_qty'],2)}}</td>
                <td>{{number_format($type['qty_var'],2)}}</td>
                <td>{{number_format($type['cost_var'],2)}}</td>
                <td>{{number_format($type['cost_variance_completion_due_qty'],2)}}</td>
                <td>{{number_format($type['cost_variance_completion_due_unit_price'],2)}}</td>
            </tr>
            </tbody>
        </table>
        @if ($type['types'] && count($type['types']))
            <ul class="list-unstyled">
                @foreach($type['types'] as $key=>$workType)
                    <li>
                        <p class="blue-second-level"
                        ><label href="#{{str_replace([' ','.'],'',$key)}}" data-toggle="collapse" style="text-decoration: none;">{{$key}}</label></p>
                        <article id="{{str_replace([' ','.'],'',$key)}}" class="tree--child collapse">
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
                                    <td>{{number_format($workType['unit_price'],2)}}</td>
                                    <td>{{number_format($workType['to_date_unit_price'],2)}}</td>
                                    <td>{{number_format($workType['unit_price_var'],2)}}</td>
                                    <td>{{number_format($workType['cost_var'],2)}}</td>
                                    <td></td>
                                    <td>{{number_format($workType['to_date_qty'],2)}}</td>
                                    <td>{{number_format($workType['qty_var'],2)}}</td>
                                    <td>{{number_format($workType['cost_var'],2)}}</td>
                                    <td>{{number_format($workType['cost_variance_completion_due_qty'],2)}}</td>
                                    <td>{{$workType['cost_variance_completion_due_unit_price']}}</td>
                                </tr>
                                </tbody>
                            </table>
                            @if ($workType['resources'] && count($workType['resources']))
                                <ul class="list-unstyled">
                                    @foreach($workType['resources'] as $rKey=>$resource)
                                        <li>
                                            <p class="blue-third-level"
                                            ><label href="#{{str_replace([' ','.'],'',$key).''.$rKey}}" data-toggle="collapse" style="text-decoration: none;">{{\App\Resources::find
                                            ($resource['resource_id'])->name}}</label></p>

                                            <article id="{{str_replace([' ','.'],'',$key).''.$rKey}}" class="tree--child collapse">
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