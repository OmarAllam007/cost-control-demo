<tr>
    <td>{{$level['name']}}</td>
    <td>{{$level['budget_cost']}}</td>
    <td>{{$level['prev_cost']}}</td>
    <td>{{$level['prev_allowable']}}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>

@foreach($tree->where('parent', $key) as $subkey => $sublevel)
    @include('reports.cost-control.activity._wbs', ['key' => $subkey, 'level' => $sublevel])
@endforeach