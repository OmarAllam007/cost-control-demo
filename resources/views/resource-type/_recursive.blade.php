<li>
    <div class="tree--item">
        <a href="#children-{{$resource_type->id}}" class="tree--item--label" data-toggle="collapse">
            <i class="fa fa-chevron-circle-right"></i> {{$resource_type->name}}
        </a>

        <span class="tree--item--actions">
            <a href="{{route('resource-type.edit', $resource_type->id)}}" class="label label-primary">
                <i class="fa fa-pencil"></i> Edit
            </a>
        </span>
    </div>

    <article id="children-{{$resource_type->id}}" class="tree--child collapse">
        @if ($resource_type->subtree->count())
            <ul class="list-unstyled">
                @foreach($resource_type->subtree->sortBy('name') as $child)
                    @include('resource-type._recursive', ['resource_type' => $child])
                @endforeach
            </ul>
        @endif

        @if ($resource_type->db_resources->count())
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th class="col-md-8">Resources</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resource_type->db_resources->sortBy('name') as $resource)
                    <tr>
                        <td>{{$resource->name}}</td>
                        <td>
                            <a href="{{route('resources.edit', $resource)}}" class="btn btn-xs btn-primary">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>
</li>

