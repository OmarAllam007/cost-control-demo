<li>
    <div class="tree--item">
        <a href="#children-{{$division->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$division->label}}</a>
        <span class="tree--item--actions">
            <a href="{{route('activity-division.show', $division)}}" class="label label-info"><i class="fa fa-eye"></i> Show</a>
            <a href="{{route('activity-division.edit', $division)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    @if ($division->children && $division->children->count())
        <ul class="list-unstyled collapse" id="children-{{$division->id}}">
            @foreach($division->children as $child)
                @include('activity-division._recursive', ['division' => $child])
            @endforeach
        </ul>
    @endif
</li>