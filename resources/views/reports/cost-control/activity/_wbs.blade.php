<tr class="level-{{$depth}} {{slug($level['parent'])}} {{$depth > 1? 'info hidden' : 'success'}}">
    <td>
        <div class="display-flex">
            <a href="#" class="open-level flex" data-target="{{slug($key)}}"><i class="fa fa-plus-square-o"></i> {{$level['name']}}</a>
            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                            'WBS' => $level['parent'] . $level['name'],
                            'Base Line' => number_format($tree->where('parent', '')->sum('budget_cost'), 2),
                            'Previous Cost' => number_format($tree->where('parent', '')->sum('prev_cost'), 2),
                            'Previous Allowable' => number_format($tree->where('parent', '')->sum('prev_allowable'), 2),
                            'Previous Var' => number_format($tree->where('parent', '')->sum('prev_cost_var'), 2),
                            'To Date Cost' => number_format($tree->where('parent', '')->sum('to_date_cost'), 2),
                            'Allowable (EV) Cost' => number_format($tree->where('parent', '')->sum('to_date_allowable'), 2),
                            'To Date Cost Var' => number_format($tree->where('parent', '')->sum('to_date_var'), 2),
                            'Remaining Cost' => number_format($tree->where('parent', '')->sum('remaining_cost'), 2),
                            'At Completion Cost' => number_format($tree->where('parent', '')->sum('completion_cost'), 2),
                            'Cost Variance' => number_format($tree->where('parent', '')->sum('completion_var'), 2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>
    <td>{{number_format($level['budget_cost'], 2)}}</td>

    <td>{{number_format($level['prev_cost'], 2)}}</td>
    <td>{{number_format($level['prev_allowable'], 2)}}</td>
    <td class="{{$level['prev_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($level['prev_cost_var'], 2)}}</td>

    <td>{{number_format($level['to_date_cost'], 2)}}</td>
    <td>{{number_format($level['to_date_allowable'], 2)}}</td>
    <td class="{{$level['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($level['to_date_var'], 2)}}</td>

    <td>{{number_format($level['remaining_cost'], 2)}}</td>

    <td>{{number_format($level['completion_cost'], 2)}}</td>
    <td class="{{$level['completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($level['completion_var'], 2)}}</td>
</tr>

@foreach($tree->where('parent', $key)->sortBy('name') as $subkey => $sublevel)
    @include('reports.cost-control.activity._wbs', ['key' => $subkey, 'level' => $sublevel, 'depth' => $depth + 1])
@endforeach

@if (isset($level['activities']))
    @foreach($level['activities'] as $name => $activity)
        @include('reports.cost-control.activity._activity', ['depth' => $depth + 1])
    @endforeach
@endif