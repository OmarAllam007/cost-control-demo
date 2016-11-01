<li>
    <div class="tree--item">
        <a href="#children-{{$category['id']}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$category['name']}}</a>
        <span class="tree--item--actions">

            <a href="{{route('csi-category.edit', $category)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    <article id="children-{{$category['id']}}" class="tree--child collapse">
        @if (count($category['children']))
            <ul class="list-unstyled">
                @foreach($category['children'] as $child)
                    @include('productivity._recursive', ['category' => $child])
                @endforeach
            </ul>
        @endif

        @if(count($category['productivities']))
            @include('productivity._list', ['productivities' => $category['productivities']])
        @else
            <div class="alert alert-info">
                <i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
            </div>
        @endif
    </article>
</li>

