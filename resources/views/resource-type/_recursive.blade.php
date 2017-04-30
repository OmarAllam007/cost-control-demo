<li>
    <div class="tree--item">
        <a href="#children-{{$resource_level->id}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$resource_level->name}}</a>
        <span class="tree--item--actions">
            <a href="{{route('resource-type.edit', $resource_level->id)}}" class="label label-primary"><i
                        class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    <article id="children-{{$resource_level->id}}" class="tree--child collapse">
        @if ($resource_level->children->count())
            <ul class="list-unstyled">
                @foreach($resource_level->children->sortBy('name') as $child)
                    @include('resource-type._recursive', ['resource_level' => $child])
                @endforeach
            </ul>
        @endif

        @php
            $resources = $resource_level->resources()->basic()->orderBy('name')->get();
        @endphp

        @if ($resources->count())
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th class="col-md-8">Resources</th>
                    <th>
                        <div class="pull-right">
                            Actions
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                            <tr>
                                <td>{{$resource->name}}</td>
                                <td>
                                    <div class="pull-right">
                                        {{--<a href="{{route('resources.show', $resource)}}" class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i> Show
                                        </a>--}}
                                        <a href="{{route('resources.edit', $resource)}}" class="btn btn-xs btn-primary">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </article>
</li>

