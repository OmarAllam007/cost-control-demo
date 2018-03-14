<tr class="info wbs-level child-{{ $level->parent_id }} {{ $depth? 'hidden' : '' }} level-{{ $depth }}">
    <th colspan="2" class="w-600 level-label">
        <a href="#" data-target="child-{{ $level->id }}" class="open-level">
            <i class="fa fa-plus-square-o"></i>
            {{ $level->name }} <small>({{ $level->code }})</small>
        </a>
    </th>
    <th class="w-200">{{ number_format($level->allowable_cost, 2) }}</th>
    <th class="w-200">{{ number_format($level->to_date_cost, 2) }}</th>
    <th class="w-200">{{ number_format($level->variance, 2) }}</th>
    <th class="w-200">{{ number_format($level->increase, 2) }}%</th>
</tr>

@foreach($level->subtree as $sublevel)
    @include('reports.cost-control.threshold.wbs_level', ['depth' => $depth + 1, 'level' => $sublevel])
@endforeach

@foreach($level->activities as $activity)
    <tr class="child-{{ $level->id }} hidden">
        <td class="w-300">&nbsp;</td>
        <td class="w-300">{{ $activity->activity }}</td>
        <td class="w-200">{{ number_format($activity->allowable_cost, 2) }}</td>
        <td class="w-200">{{ number_format($activity->to_date_cost, 2) }}</td>
        <td class="w-200">{{ number_format($activity->variance, 2) }}</td>
        <td class="w-200">{{ number_format($activity->increase, 2) }}%</td>
    </tr>
@endforeach

