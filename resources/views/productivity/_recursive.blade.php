<li>
    <div class="tree--item">
        <a href="#children-{{$category->id}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$category->name}}</a>
        <span class="tree--item--actions">
            <a href="{{route('productivity.show', $category)}}" class="label label-info"><i class="fa fa-eye"></i> Show</a>
            <a href="{{route('productivity.edit', $category)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    <article id="children-{{$category->id}}" class="tree--child collapse">


        @if ($category->children->count())
            <ul class="list-unstyled">
                @foreach($category->children as $child)
                    @include('productivity._recursive', ['category' => $child])
                @endforeach
            </ul>
        @endif
        @if($category->productivity)
            <table class="table table-condensed table-striped">
                <thead>
                <tr>

                    <th>Crew Structure</th>
                    <th>Unit</th>
                    {{--<th>crew hours</th>--}}
                    {{--<th>crew equip</th>--}}
                    {{--<th>daily output</th>--}}
                    {{--<th>man hours</th>--}}
                    {{--<th>equip hours</th>--}}
                    {{--<th>reduction factor</th>--}}
                    {{--<th>after reduction</th>--}}
                    {{--<th>source</th>--}}
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($category->productivity as $productivity)
                    <tr>

                        <td class="col-md-1">{{ $productivity->crew_structure }}
                        </td>
                        <td class="col-md-1">{{ isset($productivity->units->type)?$productivity->units->type:'' }}
                        </td>

                        <td class="col-md-2">
                            <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('productivity.edit', $productivity) }} "><i
                                            class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach


                </tbody>
            </table>

        @else

            <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity
                    found</strong>

            </div>

        @endif
    </article>
</li>

