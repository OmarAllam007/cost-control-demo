@if($level['activities'] && count($level['activities']))
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
            <a href="#{{$level['id']}}" class="
       @if($tree_level ==0)
                    blue-first-level
                 @elseif($tree_level ==1)
                    blue-third-level
                   @else
                    blue-fourth-level
                        @endif
                    " data-toggle="collapse" style="@if($tree_level ==0) color:white; @endif text-decoration: none">
                {{$level['name']}} | Original BOQ : {{number_format($level['original_boq'])}} | Revised BOQ : {{number_format($level['revised_boq'])}}
            </a>

        </p>

        <article id="{{$level['id']}}" class="tree--child collapse">
            <ul class="list-unstyled">
                <li>
                    @foreach($level['activities'] as $keyActivity=>$activity)
                        <p class="blue-second-level">
                            <a href="#{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$activity['name'])}}" class=" " data-toggle="collapse"
                               style="@if($tree_level ==0) color:white; @endif text-decoration: none">
                                {{$activity['name']}} | Original BOQ : {{number_format($activity['original_boq'])}} | Revised BOQ : {{number_format($activity['revised_boq'])}}
                            </a>

                        </p>
                        @if($activity['cost_accounts'] && count($activity['cost_accounts']))
                            <article id="{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$activity['name'])}}" class="tree--child collapse">
                                <ul class="list-unstyled">
                                    <li>
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr class="output-header">
                                                <th>COST ACCOUNT</th>
                                                <th>Original BOQ</th>
                                                <th>Revised BOQ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($activity['cost_accounts'] as $keyCostAccount=>$cost_account)
                                                <tr>
                                                    <td>
                                                        {{$cost_account['cost_account']}}
                                                    </td>

                                                    <td>
                                                        {{number_format($cost_account['original_boq'],2)}}
                                                    </td>

                                                    <td>
                                                        {{number_format($cost_account['revised_boq'],2)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </article>
                        @endif
                    @endforeach
                </li>
            </ul>
            {{--@if ($level['children'] && count($level['children']))--}}
            {{--<ul class="list-unstyled">--}}
            {{--@foreach($level['children'] as $child)--}}
            {{--@include('reports.budget.resource_dictionary._recursive_resource_dictionary', ['level' => $child, 'tree_level' => $tree_level + 1])--}}
            {{--@endforeach--}}
            {{--</ul>--}}
            {{--@endif--}}


        </article>

    </li>
@endif