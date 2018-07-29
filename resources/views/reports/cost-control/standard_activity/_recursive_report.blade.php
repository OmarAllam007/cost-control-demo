@php
    $subs = $tree->where('parent', $name);
@endphp

<tr class="level-{{$level['index']}} {{$level['index'] > 0 ? 'hidden' : ''}} {{$level['parent'] ? slug($level['parent']) : ''}}">
    <td class="col-xs-2 level-label">
        <div class="display-flex">
            <a href="#" data-target="{{slug($name)}}" class="open-level flex"><i class="fa fa-plus-square-o"></i> {{$name}}</a>
            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                            'Division' => $name,
                            'Budget Cost' => number_format($level['budget_cost']??0,2) ,
                            'Previous Cost' => number_format($level['previous_cost']??0,2),
                            'Previous Allowable' => number_format($level['previous_allowable']??0,2),
                            'Previous Var' => number_format($level['previous_var']??0,2),
                            'To Date Cost' => number_format($level['to_date_cost']?? 0,2),
                            'Allowable (EV) Cost' => number_format($level['to_date_allowable']??0,2),
                            'To Date Variance' => number_format($level['to_date_var']??0,2),
                            'Remaining Cost' => number_format($level['remaining']??0,2),
                            'At Completion Cost' => number_format($level['at_completion_cost']??0,2),
                            'Cost Variance' => number_format($level->cost_var??0,2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>
    <td class="col-xs-1">{{number_format($level['budget_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['previous_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['previous_allowable'], 2)}}</td>
    <td class="col-xs-1 {{$level['previous_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($level['previous_var'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['to_date_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['to_date_allowable'], 2)}}</td>
    <td class="col-xs-1 {{$level['to_date_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($level['to_date_var'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['remaining_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['completion_cost'], 2)}}</td>
    <td class="col-xs-1 {{$level['completion_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($level['completion_var'], 2)}}</td>
</tr>

@if ($subs->count())
    @foreach($subs as $subname => $sublevel)
        @include('reports.cost-control.standard_activity._recursive_report', ['name' => $subname, 'level' => $sublevel])
    @endforeach
@endif


@if (!empty($level['activities']))
    @foreach($level['activities'] as $activity)
        <tr class="level-{{$level['index'] + 1}} level-activity hidden {{slug($name)}}">
            <td class="col-xs-2 level-label">
                <div class="display-flex">
                    <span class="flex"><i class="fa fa-caret-right"></i> {{$activity['name']}}</span>
                    <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                       data-data="{{json_encode([
                            'Activity' => $activity['name'],
                            'Budget Cost' => number_format($activity['budget_cost']??0,2) ,
                            'Previous Cost' => number_format($activity['previous_cost']??0,2),
                            'Previous Allowable' => number_format($activity['previous_allowable']??0,2),
                            'Previous Var' => number_format($activity['previous_var']??0,2),
                            'To Date Cost' => number_format($activity['to_date_cost']?? 0,2),
                            'Allowable (EV) Cost' => number_format($activity['to_date_allowable']??0,2),
                            'To Date Variance' => number_format($activity['to_date_var']??0,2),
                            'Remaining Cost' => number_format($activity['remaining']??0,2),
                            'At Completion Cost' => number_format($activity['at_completion_cost']??0,2),
                            'Cost Variance' => number_format($activity->cost_var??0,2),
                           ]) }}">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </a>
                </div>
            </td>
            <td class="col-xs-1">{{number_format($activity['budget_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['previous_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['previous_allowable'], 2)}}</td>
            <td class="col-xs-1 {{$activity['previous_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['previous_var'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['to_date_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['to_date_allowable'], 2)}}</td>
            <td class="col-xs-1 {{$activity['to_date_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['to_date_var'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['remaining_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['completion_cost'], 2)}}</td>
            <td class="col-xs-1 {{$activity['completion_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['completion_var'], 2)}}</td>
        </tr>
    @endforeach
@endif