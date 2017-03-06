

<li>
    <p class="
       @if($tree_level ==0)
            blue-second-level
         @elseif($tree_level ==1)
            blue-third-level
           @else
            blue-fourth-level
                @endif
            "
    ><label href="#col-{{$division->id}}" data-toggle="collapse" style="text-decoration: none;">{{$division->label}}</label></p>

    <article id="col-{{$division->id}}" class="tree--child collapse">
        @if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count())
            <ul class="list-unstyled">
                @foreach($division->children()->whereIn('id', $all)->get() as $child)
                    @include('std-activity._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif

        @if ($division->activities()->whereIn('id',$activity_ids)->get() && $division->activities()->whereIn('id',$activity_ids)->count())
            <table class="table table-condensed">

                <tbody>
                @foreach($division->activities()->whereIn('id',$activity_ids)->get() as $activity)
                    <tr>
                        <td>{{$activity->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>


</li>