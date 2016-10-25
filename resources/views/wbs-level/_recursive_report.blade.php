<tr>
    @for($i = 0; $i < $tree_level; ++$i)
        <td>&nbsp;</td>
    @endfor

    <td
            @if($tree_level ==0)
            class="blue-second-level"
            @elseif($tree_level ==1)
            class="blue-third-level"
            @elseif($tree_level ==2)
            class="blue-fourth-level"
            @else
            class="normal-row"
            @endif
    >

        {{$wbs_level->name}}
        <small class="text-muted @if($tree_level==0)wbs-code @else '' @endif">({{$wbs_level->code}})
        </small>
    </td>

    @for ($i = $tree_level + 1; $i < 4; ++$i)
        <td
                @if($tree_level ==0)
                class="blue-second-level"
                @elseif($tree_level ==1)
                class="blue-third-level"
                @elseif($tree_level ==2)
                class="blue-fourth-level"
                @endif
        >

        </td>
    @endfor
</tr>

@if ($wbs_level->children && $wbs_level->children->count())
    @foreach($wbs_level->children as $child)
        @include('wbs-level._recursive_report', ['wbs_level' => $child, 'tree_level' => $tree_level + 1])
    @endforeach
@endif