<tr class="level-{{ $tree_level }} child-{{ $wbs_level->parent_id }} {{ $tree_level ? 'hidden' : '' }}">
    <td class="cols-m-{{$includeCost?9:12}} level-label {{ $wbs_level->subtree->count() ? 'text-strong' : '' }}">
        @if($wbs_level->subtree->count())
            <a href="#" class="open-level" data-target="child-{{ $wbs_level->id }}" data-open="false">
                <i class="fa fa-plus-square"></i>
                {{$wbs_level['name']}} &mdash; <small>({{$wbs_level['code']}})</small>
            </a>
        @else
            <i class="fa fa-angle-right"></i>
            {{$wbs_level['name']}} &mdash; <small>({{$wbs_level['code']}})</small>
        @endif
        
    </td>
    @if ($includeCost)
    <td class="col-sm-3 {{ $wbs_level->subtree->count()? 'text-strong' : '' }}">{{ number_format($wbs_level->cost, 2) }}</td>
    <td class="col-sm-2 {{ $wbs_level->subtree->count()? 'text-strong' : '' }}">{{ number_format($wbs_level->weight, 2) }}%</td>
    @endif
</tr>

@if ($wbs_level->subtree && count($wbs_level->subtree))
    @foreach($wbs_level->subtree as $child)
        @include('reports.budget.wbs._recursive', ['wbs_level' => $child, 'tree_level' => $tree_level +1])
    @endforeach
@endif