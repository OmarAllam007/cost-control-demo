<tr class="level-{{ $depth }} child-{{ $wbs_level->parent_id }} {{ $depth ? 'hidden' : '' }}">
    <td class="level-label {{ $wbs_level->subtree->count() ? 'text-strong' : '' }}" colspan="6">
        <a href="#" class="open-level" data-target="child-{{ $wbs_level->id }}" data-open="false">
            <i class="fa fa-plus-square"></i> {{$wbs_level['name']}} &mdash;
            <small>({{$wbs_level['code']}})</small>
        </a>
    </td>
</tr>

@foreach($wbs_level->subtree as $child)
    @include('reports.budget.qs-summary._recursive', ['wbs_level' => $child, 'depth' => $depth +1])
@endforeach
{{--
@if($wbs_level->activities->count())
{{dd($wbs_level->activities)}}
@endif--}}

@foreach($wbs_level->activities as $name => $group)
    <tr class="level-{{ $depth + 1 }} child-{{ $wbs_level->id }} hidden">
        <td class="level-label text-strong" colspan="6">
            <a href="#" class="open-level" data-target="group-{{ $wbs_level->id }}-{{slug($name)}}" data-open="false">
                <i class="fa fa-plus-square"></i> {{$name}}
            </a>
        </td>
    </tr>

    @foreach($group as $activity)
        <tr class="level-{{ $depth + 2 }} group-{{ $wbs_level->id }}-{{slug($name)}} hidden">
            <td class="level-label text-strong" colspan="6">
                <a href="#" class="open-level" data-target="activity-{{ $wbs_level->id }}-{{$activity->id}}" data-open="false">
                    <i class="fa fa-plus-square"></i> {{$activity->name}}
                </a>
            </td>
        </tr>
        @foreach($activity->cost_accounts as $cost_account)
            <tr class="level-{{ $depth + 3 }} activity-{{ $wbs_level->id }}-{{$activity->id}} hidden">
                <td>&nbsp;</td>
                <td class="text-center">{{$cost_account->cost_account}}</td>
                <td>{{$cost_account->boq_description}}</td>
                <td class="text-center">{{number_format($cost_account->eng_qty, 2)}}</td>
                <td class="text-center">{{number_format($cost_account->budget_qty, 2)}}</td>
                <td class="text-center">{{$cost_account->unit_of_measure}}</td>
            </tr>
        @endforeach
    @endforeach
@endforeach
