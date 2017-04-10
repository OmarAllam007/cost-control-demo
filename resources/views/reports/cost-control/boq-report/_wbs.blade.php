<tr class="level-{{$depth}} {{$depth > 1? 'hidden success' : 'info'}} {{slug($level['parent'])}}">
    <td class="cost-account-cell"><a href="#" data-target="{{slug($level['key'])}}"><i class="fa fa-plus-circle"></i> {{$level['name']}}</a></td>
    <td class="label-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell">{{number_format($level['dry_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($level['boq_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($level['budget_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($level['to_date_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($level['to_date_allowable'], 2)}}</td>
    <td class="number-cell {{$level['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($level['to_date_var'], 2)}}</td>
    <td class="number-cell">{{number_format($level['remaining_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($level['at_completion_cost'], 2)}}</td>
    <td class="number-cell {{$level['at_completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($level['at_completion_var'], 2)}}</td>
</tr>

@foreach($tree->where('parent', $key)->sortBy('name') as $subKey => $subLevel)
    @include('reports.cost-control.boq-report._wbs', ['depth' => $depth + 1, 'key' => $subKey, 'level' => $subLevel])
@endforeach

@if (!empty($level['boqs']))
    @foreach($level['boqs'] as $boq)
        @include('reports.cost-control.boq-report._boq', ['depth' => $depth + 1])
    @endforeach
@endif