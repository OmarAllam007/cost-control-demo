<tr class="level-{{ $tree_level }} child-{{ $wbs_level->parent_id }} {{ $tree_level ? 'hidden' : '' }}">
    <td class="level-label {{ $wbs_level->subtree->count() ? 'text-strong' : '' }}" colspan="5">
        <a href="#" class="open-level" data-target="child-{{ $wbs_level->id }}" data-open="false">
            <i class="fa fa-plus-square"></i> {{$wbs_level['name']}} &mdash;
            <small>({{$wbs_level['code']}})</small>
        </a>
    </td>
    <td class="col-sm-1 {{ $wbs_level->subtree->count()? 'text-strong' : '' }}">{{ number_format($wbs_level->cost, 2) }}</td>
    <td class="col-sm-1 {{ $wbs_level->subtree->count()? 'text-strong' : '' }}">{{ number_format($wbs_level->weight, 2) }}%</td>
</tr>


@foreach($wbs_level->subtree as $child)
    @include('reports.budget.wbs-labours._recursive', ['wbs_level' => $child, 'tree_level' => $tree_level +1])
@endforeach

@foreach($wbs_level->resource_dict as $resource)
    <tr class="child-{{ $wbs_level->id }} hidden">
        <td class="col-sm-3">&nbsp;</td>
        <td class="col-sm-3">{{$resource->resource_name}}</td>
        <td class="col-sm-2">{{$resource->resource_code}}</td>
        <td class="col-sm-1">{{number_format($resource->budget_unit, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->unit_price)}}</td>
        <td class="col-sm-1">{{number_format($resource->cost)}}</td>
        <td class="col-sm-1">{{number_format($resource->weight, 2)}}%</td>
    </tr>
@endforeach

