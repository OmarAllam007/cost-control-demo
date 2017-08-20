<tr class="level-{{ $depth }} child-{{ $wbs_level->parent_id }} {{ $depth ? 'hidden' : '' }}">
    <td class="level-label {{ $wbs_level->subtree->count() ? 'text-strong' : '' }}">

        @if ($wbs_level->subtree->count())
        <a href="#" class="open-level" data-target="child-{{ $wbs_level->id }}" data-open="false">
            <i class="fa fa-plus-square"></i> {{$wbs_level['name']}} &mdash;
            <small>({{$wbs_level['code']}})</small>
        </a>
        @else
            <i class="fa fa-caret-right"></i> {{$wbs_level['name']}} &mdash;
            <small>({{$wbs_level['code']}})</small>
        @endif
    </td>
    <td>{{number_format($wbs_level->cost, 2)}}</td>
    <td>{{number_format($wbs_level->dry_cost, 2)}}</td>
    <td class="{{$wbs_level->difference < 0? 'text-danger' : ''}}">{{number_format($wbs_level->difference, 2)}}</td>
    <td class="{{$wbs_level->difference < 0? 'text-danger' : ''}}">{{number_format($wbs_level->increase, 2)}}</td>
</tr>

@foreach($wbs_level->subtree as $child)
    @include('reports.budget.budget_cost_vs_dr_by_building._recursive', ['wbs_level' => $child, 'depth' => $depth +1])
@endforeach