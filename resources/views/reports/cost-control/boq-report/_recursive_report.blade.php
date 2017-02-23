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
        <a href="#col-{{$level['id']}}" data-toggle="collapse"
           style="@if($tree_level==0) color: white;  @elseif($tree_level==1) color: black; @elseif($tree_level==2) color: black; @endif text-decoration: none;">{{$level['name']}}</a>
    </p>
    <article id="col-{{$level['id']}}" class="tree--child collapse">
        @if(count($level['division']))
            <ul class="tree list-unstyled">
                @foreach($level['division'] as $divKey=>$division)
                    <li>
                        <p class="blue-second-level"><label
                                    href="#{{$level['id']}}{{$divKey}}"
                                    data-toggle="collapse">{{$division['name']}}</label></p>
                        <article class="tree--child collapse" id="{{$level['id']}}{{$divKey}}">
                            @if(count($division['activities']))
                                <table class="table table-condensed">
                                    <thead style="background:#95DAC2;color: #000; border-bottom: solid black">
                                    <tr>
                                        <td>Activity</td>
                                        <td>Budget Cost</td>
                                        <td>Previous Cost</td>
                                        <td>Previous Allowable Cost</td>
                                        <td>Previous Allowable Var</td>
                                        <td>Todate Cost</td>
                                        <td>Allowable Cost</td>
                                        <td>Allowable Var</td>
                                        <td>Remain Cost</td>
                                        <td>Completion Cost</td>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($division['activities'] as $activity)
                                        @if($activity['name'])
                                        <tr>
                                            <td>{{$activity['name']}} </td>
                                            <td>{{number_format($activity['budget_cost']) ?? 0}} </td>
                                            <td>{{number_format($activity['prev_cost']) ?? 0}} </td>
                                            <td>{{number_format($activity['prev_allowable']) ?? 0}} </td>
                                            <td>{{number_format($activity['prev_var']) ?? 0}} </td>
                                            <td>{{number_format($activity['to_date_cost']) ?? 0}}</td>
                                            <td>{{number_format($activity['allowable_cost'] ?? 0)}}</td>
                                            <td>{{number_format($activity['allowable_var']) ?? 0}}</td>
                                            <td>{{number_format($activity['remain_cost']) ?? 0}}</td>
                                            <td>{{number_format($activity['completion_cost']) ?? 0}}</td>

                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.cost-control.boq-report._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif

    </article>


</li>