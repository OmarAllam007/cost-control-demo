<tr class="level-{{$depth}} child-{{$level->parent_id}} {{$depth? 'hidden' : ''}}">
    <td colspan="6" class="level-label">
        <a href="#" class="open-level" data-target="child-{{$level->id}}">
            <strong><i class="fa fa-plus-square"></i> {{$level->name}} <small>({{$level->code}})</small></strong>
        </a>
    </td>
    <td><strong>{{number_format($level->cost, 2)}}</strong></td>
</tr>

@forelse($level->subtree as $sublevel)
    @include('reports.budget.activity_resource_breakdown._recursive', ['level' => $sublevel, 'depth' => $depth + 1])
@empty
@endforelse

@forelse($level->activities as $activity => $cost_accounts)
    <tr class="level-{{$depth + 1}} child-{{$level->id}} hidden">
        <td class="level-label" colspan="6">
            <a href="#" class="open-level" data-target="activity-{{$level->id}}-{{slug($activity)}}">
                <strong><i class="fa fa-plus-square"></i> {{$activity}}</strong>
            </a>
        </td>

        <td><strong>{{number_format($cost_accounts->flatten()->sum('budget_cost'), 2)}}</strong></td>
    </tr>

    @forelse($cost_accounts as $cost_account => $resources)
        <tr class="level-{{$depth + 2}} activity-{{$level->id}}-{{slug($activity)}} hidden">
            <td class="level-label" colspan="6">
                <a href="#" class="open-level" data-target="resources-{{$level->id}}-{{$cost_account}}">
                    <strong>
                        <i class="fa fa-plus-square"></i>
                        {{$cost_account}} &mdash;
                        @if ($level->boqs->has($cost_account))
                            ({{$level->boqs->get($cost_account)->description}})
                        @else
                            <span class="text-danger">(BOQ is not found)</span>
                        @endif
                    </strong>
                </a>
            </td>
        </tr>
    @empty
    @endforelse
@empty
@endforelse