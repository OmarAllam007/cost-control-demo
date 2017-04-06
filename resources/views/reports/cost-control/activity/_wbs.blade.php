<tr class="level-{{$depth}} {{slug($level['parent'])}} {{$depth > 1? 'info hidden' : 'success'}}">
    <td><a href="#" class="open-level" data-target="{{slug($key)}}"><i class="fa fa-plus-square-o"></i> {{$level['name']}}</a></td>
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