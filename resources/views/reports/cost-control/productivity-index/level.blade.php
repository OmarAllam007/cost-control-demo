<tr class="info child-{{$level->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td class="level-label w-400">
        <a href="#" data-target="child-{{$level->id}}" class="open-level">
            <i class="fa fa-plus-square-o"></i>
            {{$level->name}}
        </a>
    </td>
    <td class="w-150">{{number_format($level->budget_unit, 2)}}</td>
    <td class="w-150">&nbsp;</td>
    <td class="w-150">{{number_format($level->allowable_qty, 2)}}</td>
    <td class="w-150">{{number_format($level->actual_man_days, 2)}}</td>
    <td class="w-150 {{$level->variance < 0? 'text-danger' : ''}}">{{number_format($level->variance, 2)}}</td>
    <td class="w-150 {{$level->pi < 0? 'text-danger' : ''}}">{{number_format($level->pi, 2)}}</td>
</tr>

@foreach($level->subtree as $sublevel)
    @include('reports.cost-control.productivity-index.level', ['level' => $sublevel, 'depth' => $depth + 1])
@endforeach

@foreach($level->labour_activities as $activity)
    <tr class="level-{{$depth+1}} child-{{$level->id}} hidden">
        <td class="w-400 level-label">{{$activity->activity}}</td>
        <td class="w-150">{{number_format($activity->budget_unit, 2)}}</td>
        <td class="w-150">{{number_format($activity->progress, 2)}}</td>
        <td class="w-150">{{number_format($activity->allowable_qty, 2)}}</td>
        <td class="w-150">{{number_format($activity->actual_man_days, 2)}}</td>
        <td class="w-150 {{$activity->variance < 0? 'text-danger' : ''}}">{{number_format($activity->variance, 2)}}</td>
        <td class="w-150 {{$activity->pi < 0? 'text-danger' : ''}}">{{number_format($activity->pi, 2)}}</td>
    </tr>
@endforeach