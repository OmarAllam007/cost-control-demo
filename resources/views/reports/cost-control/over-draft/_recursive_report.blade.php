<li>
    <p class="
       @if($tree_level ==0)
            blue-first-level
         @elseif($tree_level ==1)
            blue-second-level
           @else
            blue-fourth-level
                @endif
            "
    >
        <a href="#col-{{$level['id']}}" data-toggle="collapse"
           style="@if($tree_level>1) color: black;  @else color: white; @endif text-decoration: none !important;">
            {{$level['name']}}
        </a>

    </p>

    <article id="col-{{$level['id']}}" class="tree--child collapse">
        @if(count($level['divisions']))
            <ul class="tree list-unstyled">
                @foreach($level['divisions'] as $key=>$division)
                    <li>
                        <p class="blue-third-level"><a href="#{{$key}}{{$level['id']}}"
                                                       data-toggle="collapse">{{$division['name']}}</a></p>
                        <article id="{{$key}}{{$level['id']}}" class="tree--child collapse">
                            @if(count($division['activities']))
                                <ul class="tree list-unstyled">
                                    @foreach($division['activities'] as $actKey=>$activity)
                                        <li>
                                            <p class="blue-fourth-level">
                                                <a href="#{{$actKey}}{{$key}}{{$level['id']}}" data-toggle="collapse">
                                                    {{$activity['activity_name']}}
                                                </a>
                                            </p>
                                            <article class="tree--child collapse" id="{{$actKey}}{{$key}}{{$level['id']}}">
                                                <table class="table table-condensed">
                                                    <thead style="background:#95DAC2;color: #000; border-bottom: solid black">
                                                    <tr>
                                                        <td>Cost Account - BOQ Description</td>
                                                        <td>Estimated BOQ Quantity</td>
                                                        <td>Actual Qty</td>
                                                        <td>BOQ U.price</td>
                                                        <td>Physical Unit</td>
                                                        <td>Physical Unit (Excluding u.p variance)</td>
                                                        <td>Physical Revenue</td>
                                                        <td>Physical Revenue (Excluding u.p variance)</td>
                                                        <td>Actual Revenue Cost</td>
                                                        <td>Variance</td>
                                                        <td>Variance (Excluding u.p variance)</td>

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($activity['cost_accounts'] as $cost_account)
                                                            <tr>
                                                            <td>{{$cost_account['cost_account']}} - {{$cost_account['description']}} </td>
                                                            <td>{{number_format($cost_account['estimated_qty'])}}</td>
                                                            <td>{{number_format($cost_account['actual_qty'])}}</td>
                                                            <td>{{number_format($cost_account['price_ur'])}}</td>
                                                            <td>{{number_format($cost_account['physical_unit'])}}</td>
                                                            <td>{{number_format($cost_account['physical_unit_e'])}}</td>
                                                            <td>{{number_format($cost_account['physical_revenue'])}}</td>
                                                            <td>{{number_format($cost_account['physical_revenue_e'])}}</td>
                                                            <td>{{number_format($cost_account['actual_revenue_cost'])}}</td>
                                                            <td>{{number_format($cost_account['variance'])}}</td>
                                                            <td>{{number_format($cost_account['variance_e'])}}</td>
                                                        </tr>
                                                    @endforeach
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


        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.cost-control.over-draft._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif
    </article>


</li>