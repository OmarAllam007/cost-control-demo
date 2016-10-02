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
        @if ($division->items->count())
            <table class="table table-striped table-hover table-condensed table-responsive">
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
                @foreach($division->items as $item)
                    <tr>
                        <td>{{$item->item_code}}</td>
                        <td>
                            <div class="pull-right">
                                <a href="{{route('boq.show', $item->id)}}" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Show
                                </a>
                                <a href="{{route('boq.edit', $item)}}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if ($division->children->count())
            <ul class="list-unstyled">
                @foreach($division->children as $child)
                    @include('boq-division._recursive2', ['division' => $child])
                @endforeach
            </ul>
        @endif



    </article>
</li>

