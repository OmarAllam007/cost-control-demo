<li>
    <div class="tree--item">
        <a href="#children-{{$division->id}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$division->name}}</a>
        <span class="tree--item--actions">

            <a href="{{route('boq-division.edit', $division->id)}}" class="label label-primary"><i
                        class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    <article id="children-{{$division->id}}" class="tree--child collapse">

        @if ($division->children->count())
            <ul class="list-unstyled">
                @foreach($division->children as $child)
                    @include('boq-division._recursive', ['division' => $child])
                @endforeach
            </ul>
        @endif

    </article>
</li>

