@php
    $subs = $tree->where('parent', $name);
@endphp

<tr class="level-{{$level['index']}} {{$level['index'] > 0 ? 'hidden' : ''}} {{$level['parent'] ? slug($level['parent']) : ''}}">
    <td class="col-xs-2 level-label">
        <a href="#" data-target="{{slug($name)}}" class="open-level"><i class="fa fa-plus-square-o"></i> {{$name}}</a>
    </td>
    <td class="col-xs-1">{{number_format($level['budget_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['previous_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['previous_allowable'], 2)}}</td>
    <td class="col-xs-1 {{$level['previous_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($level['previous_var'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['to_date_cost'], 2)}}</td>
    <td class="col-xs-1">{{number_format($level['to_date_allowable'], 2)}}</td>
    <td class="col-xs-1" class="{{$level['to_date_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($level['to_date_var'], 2)}}</td>
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
            <td class="col-xs-2 level-label"><i class="fa fa-caret-right"></i> {{$activity['name']}}</td>
            <td class="col-xs-1">{{number_format($activity['budget_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['previous_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['previous_allowable'], 2)}}</td>
            <td class="col-xs-1 {{$activity['previous_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['previous_var'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['to_date_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['to_date_allowable'], 2)}}</td>
            <td class="col-xs-1" class="{{$activity['to_date_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['to_date_var'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['remaining_cost'], 2)}}</td>
            <td class="col-xs-1">{{number_format($activity['completion_cost'], 2)}}</td>
            <td class="col-xs-1 {{$activity['completion_var'] >= 0? 'text-success' : 'text-danger'}}">{{number_format($activity['completion_var'], 2)}}</td>
        </tr>
    @endforeach
@endif