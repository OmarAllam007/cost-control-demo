<tr>
    @for($i = 0; $i < $tree_level; ++$i)
        <td>&nbsp;</td>
    @endfor

    <td
            @if($tree_level==0)
            class="blue-second-level"
            @endif

            @if($tree_level==1)
                class="blue-third-level"
            @endif

            @if($tree_level==2)
                class="blue-fourth-level"
            @endif

            @if($tree_level==3)
                class="blue-second-level"
            @endif

            @if($tree_level==4)
                class="blue-third-level"
            @endif

            @if($tree_level==5)
                class="blue-fourth-level"
            @endif

            @if($tree_level==6)
                class="blue-second-level"
            @endif

    >

        {{$wbs_level['name']}} -
        <small class=" @if($tree_level==0)wbs-code @else '' @endif">({{$wbs_level['code']}})
        </small>
    </td>

    @for ($i = $tree_level + 2; $i < $depthTree; ++$i)
        <td
                @if($tree_level==0)
                class="blue-second-level"
                @endif

                @if($tree_level==1)
                class="blue-third-level"
                @endif

                @if($tree_level==2)
                class="blue-fourth-level"
                @endif

                @if($tree_level==3)
                class="blue-second-level"
                @endif

                @if($tree_level==4)
                class="blue-third-level"
                @endif

                @if($tree_level==5)
                class="blue-fourth-level"
                @endif

                @if($tree_level==6)
                class="blue-second-level"
                @endif
                        >

        </td>
    @endfor
</tr>

@if ($wbs_level['children'] && count($wbs_level['children']))
    @foreach($wbs_level['children'] as $child)
        @include('wbs-level._recursive_report', ['wbs_level' => $child, 'tree_level' => $tree_level +1])
    @endforeach
@endif