<tr class="info child-{{$level->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td class="level-label w-400">
        <div class="display-flex">
            <a href="#" data-target="child-{{$level->id}}" class="open-level flex">
                <i class="fa fa-plus-square-o"></i>
                {{$level->name}}
            </a>

            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                                    'WBS Level' => $level->path,
                                    'Budget Unit' => number_format($level->budget_unit, 2),
                                    'Sum of Earned Mandays' => number_format($level->allowable_qty, 2),
                                    'Sum of Actual Mandays' => number_format($level->actual_man_days, 2),
                                    'Sum of Variance' => number_format($level->variance, 2),
                                    'P.I.' => number_format($level->pi, 2),
                                ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
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
        <td class="w-400 level-label">
            <div class="display-flex">
                <span class="flex">{{$activity->activity}}</span>

                <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                   data-data="{{json_encode([
                                    'Activity' => $level->path . ' / ' . $activity->activity,
                                    'Progress' => number_format($activity->progress, 2),
                                    'Budget Unit' => number_format($activity->budget_unit, 2),
                                    'Sum of Earned Mandays' => number_format($activity->allowable_qty, 2),
                                    'Sum of Actual Mandays' => number_format($activity->actual_man_days, 2),
                                    'Sum of Variance' => number_format($activity->variance, 2),
                                    'P.I.' => number_format($activity->pi, 2),
                                ]) }}">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </a>
            </div>
        </td>
        <td class="w-150">{{number_format($activity->budget_unit, 2)}}</td>
        <td class="w-150">{{number_format($activity->progress, 2)}}</td>
        <td class="w-150">{{number_format($activity->allowable_qty, 2)}}</td>
        <td class="w-150">{{number_format($activity->actual_man_days, 2)}}</td>
        <td class="w-150 {{$activity->variance < 0? 'text-danger' : ''}}">{{number_format($activity->variance, 2)}}</td>
        <td class="w-150 {{$activity->pi < 0? 'text-danger' : ''}}">{{number_format($activity->pi, 2)}}</td>
    </tr>
@endforeach