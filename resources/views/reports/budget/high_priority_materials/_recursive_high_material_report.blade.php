@if(($level['budget_cost'] > 0 ))
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
            <a href="#{{$level['id']}}"  class="
       @if($tree_level ==0)
                    blue-first-level
                 @elseif($tree_level ==1)
                    blue-third-level
                   @else
                    blue-fourth-level
                        @endif
                    " data-toggle="collapse"  style="@if($tree_level ==0) color:white; @endif text-decoration: none" >
                {{$level['name']}}  || {{number_format($level['budget_cost'],2)}}
            </a>

        </p>

        <article id="{{$level['id']}}" class="tree--child collapse">
        @if(count($level['resources']))
            <ul class="list-unstyled">
                <li>
                    <table class="table table-condensed">
                        <thead>
                        <tr class="blue-second-level">
                            <th></th>
                            <th class="col-xs-1">RESOURCE NAME</th>
                            <th class="col-xs-1">BUDGET UNIT</th>
                            <th class="col-xs-1">BUDGET COST</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($level['resources'] as $keyResource=>$resource)
                            <tr class="tbl-content">
                                <td class="col-sm-1"><input type="checkbox" name="checked[]" class="checkList"
                                                            value="{{$resource['id']}}"></td>
                                <td class="col-xs-1 blue-fourth-level">
                                    {{$resource['name']}}
                                </td>
                                <td class="col-xs-1">{{number_format($resource['budget_unit'],2)}}</td>
                                <td class="col-xs-1">{{number_format($resource['budget_cost'],2)}}</td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </li>
            </ul>
            @endif
            @if ($level['children'] && count($level['children']))
                <ul class="list-unstyled">
                    @foreach($level['children'] as $child)
                        @include('reports.budget.high_priority_materials._recursive_high_material_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                    @endforeach
                </ul>
            @endif


        </article>

    </li>
@endif