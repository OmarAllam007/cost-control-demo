{{--{{dd($level)}}--}}
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
        <a href="#{{$level['id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$level['name']}}
        </a>

        <span class="pull-right">{{number_format($level['activities_total_cost'],2)}}</span></p>

    <article id="{{$level['id']}}" class="tree--child collapse">

        @if($level['activities'] && count($level['activities']))
            @foreach($level['activities'] as $item=>$activity)
                <ul class="list-unstyled">
                    <li>
                        <p class="blue-second-level">
                            <a href="#{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$item)}}" data-toggle="collapse" style="color:white;text-decoration: none">
                                {{$item}}
                            </a>
                            <span class="pull-right">{{number_format($activity['activity_total_cost'],2)}}</span>
                        </p>
                        <article id="{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$item)}}" class="tree--child collapse">
                            @foreach($activity['cost_accounts'] as $costKey=>$cost_account)
                                <ul class="list-unstyled">
                                    <li>
                                        <p class="blue-fourth-level" style="text-align: center">
                                            {{$cost_account['cost_account']}} - <abbr @if($cost_account['boq_description']==0) style="color: red" @endif>( @if($cost_account['boq_description']==0) Cost Account Not Exist In BOQ @else {{$cost_account['boq_description']}} @endif
                                                </abbr>
{{--                                            <span class="pull-right">{{number_format($cost_account['account_total_cost'],2)}}</span>--}}

                                        </p>

                                        <article>
                                            <table class="table table-condensed ">
                                                <thead>
                                                <tr class="tbl-children-division">
                                                    <th class="col-md-2">Resource Name</th>
                                                    <th class="col-md-2">Price-Unit</th>
                                                    <th class="col-md-2">Unit of Measure</th>
                                                    <th class="col-md-2">Budget Unit</th>
                                                    <th class="col-md-2">Budget Cost</th>
                                                    <th class="col-md-2">Resource Type</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($cost_account['resources'] as $resource)
                                                    <tr class="tbl-content">
                                                        <td class="col-md-2">{{$resource['name']}}</td>

                                                        <td class="col-md-2">{{number_format($resource['price_unit'],2)}}</td>
                                                        <td class="col-md-2">{{$resource['unit']}}</td>
                                                        <td class="col-md-2">{{number_format($resource['budget_unit'],2)}}</td>
                                                        <td class="col-md-2">{{number_format($resource['budget_cost'],2)}}</td>
                                                        <td class="col-md-2">{{$resource['resource_type']}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </article>
                                    </li>
                                </ul>
                            @endforeach
                        </article>
                    </li>
                </ul>
            @endforeach
        @endif





        @if (isset($level['children']) && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.budget.activity_resource_breakdown._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>

        @endif


    </article>

</li>