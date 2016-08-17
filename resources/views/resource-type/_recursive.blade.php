<li>
    <div class="tree--item">
        <a href="#children-{{$resource_level->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$resource_level->name}}</a>
        <span class="tree--item--actions">
            <a href="{{route('resource-type.show', $resource_level)}}" class="label label-info"><i class="fa fa-eye"></i> Show</a>
            <a href="{{route('resource-type.edit', $resource_level)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    @if ($resource_level->children && $resource_level->children->count())
        <ul class="list-unstyled collapse" id="children-{{$resource_level->id}}">
            @foreach($resource_level->children as $child)
                @include('resource-type._recursive', ['resource_level' => $child])
            @endforeach
        </ul>
    @endif
</li>