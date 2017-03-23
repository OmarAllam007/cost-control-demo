@if(($level['resources'] && count($level['resources'])) || ($level['children'] && count($level['children'])))
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
        <a href="#col-{{$level['id']}}"  class="
       @if($tree_level ==0)
                blue-first-level
             @elseif($tree_level ==1)
                blue-third-level
               @else
                blue-fourth-level
                    @endif
                " data-toggle="collapse"  style="@if($tree_level ==0) color:white; @endif text-decoration: none" >
            {{$level['name']}} @if($level['budget_cost']) ||  {{number_format($level['budget_cost'],2)}} @endif
        </a>

    </p>

    <article id="col-{{$level['id']}}" class="tree--child collapse level-container">

            <ul class="list-unstyled">
                    <li>
                        <table class="table table-condensed">
                            <thead>
                            <tr class="blue-second-level">
                                <th class="col-xs-1">CODE</th>
                                <th class="col-xs-1">RESOURCE NAME</th>
                                <th class="col-xs-1">UNIT</th>
                                <th class="col-xs-1">RATE</th>
                                <th class="col-xs-1">SUPPLIER/SUBCON.</th>
                                <th class="col-xs-1">REFERENCE</th>
                                <th class="col-xs-1">Waste %</th>
                                <th class="col-xs-1">BUDGET UNIT</th>
                                <th class="col-xs-1">BUDGET COST</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($level['resources'] as $keyResource=>$resource)

                                <tr>
                                    <td class="col-xs-1">
                                        {{$resource['code']}}
                                    </td>
                                    <td class="col-xs-1 blue-fourth-level">
                                        {{$resource['name']}}
                                    </td>
                                    <td class="col-xs-1">
                                        {{$resource['unit']}}
                                    </td>
                                    <td class="col-xs-1 blue-fourth-level">
                                        {{number_format($resource['rate'],2)}}
                                    </td>
                                    <td class="col-xs-1">
                                        {{$resource['partner']}}
                                    </td>

                                    <td class="col-xs-1 blue-fourth-level">
                                        {{$resource['reference']}}
                                    </td>
                                    <td class="col-xs-1">{{number_format($resource['waste'],2)}}
                                        %
                                    </td>
                                    <td class="col-xs-1">{{number_format($resource['budget_unit'],2)}}</td>
                                    <td class="col-xs-1">{{number_format($resource['budget_cost'],2)}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </li>
            </ul>
        {{--@endif--}}
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.budget.resource_dictionary._recursive_resource_dictionary', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif


    </article>

</li>
    @endif