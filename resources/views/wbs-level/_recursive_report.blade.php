<tr>
    @for($i = 0; $i < $tree_level; ++$i)
        <td>&nbsp;</td>
    @endfor
    <td>
        {{$wbs_level->name}}
        <small class="text-muted">({{$wbs_level->code}})</small>
    </td>
    @for ($i = $tree_level + 1; $i < 4; ++$i)
        <td>&nbsp;</td>
    @endfor
</tr>

@if ($wbs_level->children && $wbs_level->children->count())
    @foreach($wbs_level->children as $child)
        @include('wbs-level._recursive_report', ['wbs_level' => $child, 'tree_level' => $tree_level + 1])
    @endforeach
@endif