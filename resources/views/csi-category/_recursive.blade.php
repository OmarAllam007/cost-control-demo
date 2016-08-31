<li>
    <div class="tree--item">
        <a href="#children-{{$csiCategory->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$csiCategory->name}}</a>
        <span class="tree--item--actions">

            <a href="{{route('csi-category.edit', $csiCategory)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    @if ($csiCategory->children && $csiCategory->children->count())
        <ul class="list-unstyled collapse" id="children-{{$csiCategory->id}}">
            @foreach($csiCategory->children as $child)
                @include('csi-category._recursive', ['csiCategory' => $child])
            @endforeach
        </ul>
    @endif
</li>