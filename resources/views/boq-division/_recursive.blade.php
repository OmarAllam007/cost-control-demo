<li>
    <div class="tree--item">
        <a href="#children-{{$division->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$division->name}}</a>
        <span class="tree--item--actions">

            <a href="{{route('boq-division.edit', $division->id)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
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

            @if ($division->items->count())
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                    <tr>
                        <th class="col-md-8">Boq Items</th>
                        <th>
                            <div class="pull-right">
                                Actions
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($division->items as $division)
                        <tr>
                            <td>{{$division->item}}</td>
                            <td>
                                <div class="pull-right">
                                    <a href="{{route('boq.show', $division->id)}}" class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i> Show
                                    </a>
                                    <a href="{{route('boq.edit', $division)}}" class="btn btn-xs btn-primary">
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

