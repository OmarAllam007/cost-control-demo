<li>
    <p class="tree--item">{{$division->label}}</p>

    <article class="tree--child ">
        @if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count())
            <ul class="list-unstyled">
                @foreach($division->children()->whereIn('id', $all)->get() as $child)
                    @include('std-activity._recursive_report', ['division' => $child])
                @endforeach
            </ul>
        @endif

        @if ($division->activities()->whereIn('id',$activity_ids)->get()&& $division->activities()->whereIn('id',$activity_ids)->count())
            <table class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th class="col-md-8">Activity</th>
                </tr>
                </thead>
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