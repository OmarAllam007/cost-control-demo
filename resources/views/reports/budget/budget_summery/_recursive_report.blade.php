@if($division['total_budget'] >0)
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
            <a href="#{{$division['division_id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
                {{$division['name']}}
            </a>

            <strong class="pull-right">{{number_format($division['total_budget'],2)}}</strong>

        </p>

        <article id="{{$division['division_id']}}" class="tree--child collapse">
            @if ($division['activities'] && count($division['activities']))
                <ul class="list-unstyled">
                    <li>
                        <table class="table  table-condensed">
                            <thead>
                            <tr class="activity-header">
                                <th>Activity</th>
                                <th width="200px"><span class="pull-right">Budget Cost</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($division['activities'] as $keyActivity=>$activity)

                                <tr>
                                    <td>{{$activity['name']}}</td>
                                    <td>
                                        <span class="pull-right">{{number_format($activity['budget_cost'],2)}}</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                    </li>
                </ul>
            @endif

            @if (isset($division['children']) && count($division['children']))
                <ul class="list-unstyled">
                    @foreach($division['children'] as $child)
                        @include('reports.budget.budget_summery._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                    @endforeach
                </ul>
            @endif



        </article>
    </li>
@endif