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
        <a href="#col-{{$level['id']}}"  data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$level['name']}}
        </a>

    </p>

    <article id="col-{{$level['id']}}" class="tree--child collapse level-container" data-code="{{strtolower($level['code'])}}">

        @if($level['divisions'] && count($level['divisions']))
            <ul class="list-unstyled">
                @foreach($level['divisions'] as $keyDivision=>$division)
                    <li>
                        <p class="blue-first-level">
                            <a  style="color: #fff;" data-toggle="collapse">{{$division['name']}}</a>
                        </p>
                        <article class="tree--child" id="{{$keyDivision}}">
                            @if ($division['activities'] && count($division['activities']))
                                <ul class="list-unstyled">
                                    @foreach($division['activities'] as $keyActivity=>$activity)
                                        <li>
                                            <p class="blue-fourth-level">
                                                <a  data-toggle="collapse">{{$activity['name']}}</a>
                                            </p>
                                            <article class="tree--child " id="activity-{{$keyActivity}}">
                                                @if ($activity['cost_accounts'] && count($activity['cost_accounts']))
                                                    <article class="tree--child">
                                                        <table class="table table-condensed">
                                                            <thead class="blue-fourth-level">

                                                            <th class="col-xs-3">Cost Account</th>
                                                            <th class="col-xs-3">Boq Description</th>
                                                            <th class="col-xs-2">Engineering Quantity</th>
                                                            <th class="col-xs-2">Budget Quantity</th>
                                                            <th class="col-xs-2">Unit of Measure</th>

                                                            </thead>
                                                            <tbody>
                                                            @foreach($activity['cost_accounts'] as $cost_account)
                                                                <tr style="font-size:10pt">
                                                                    <td class="col-xs-3">
                                                                        {{$cost_account['cost_account']}}
                                                                    </td>
                                                                    <td class="col-xs-3">
                                                                        {{$cost_account['boq_name']}}
                                                                    </td>
                                                                    <td class="col-xs-2">
                                                                        {{number_format($cost_account['eng_qty'],2)}}
                                                                    </td>

                                                                    <td class="col-xs-2">
                                                                        {{number_format($cost_account['budget_qty'],2)}}
                                                                    </td>
                                                                    <td class="col-xs-2">
                                                                        {{$cost_account['unit']}}

                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </article>
                                                @endif
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
                    @include('reports.budget.qs_summery._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif


    </article>

</li>