@foreach($divisions as $division)
    <li>
        <div class="tree--item">
            <a href="#children-{{$division['id']}}" class="tree--item--label" data-toggle="collapse"><i
                        class="fa fa-chevron-circle-right"></i> {{$division['name']}}</a>
            <span class="tree--item--actions">
        </span>
        </div>
        @if(!empty($division['items']))
            <article id="children-{{$division['id']}}" class="tree--child collapse">
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
                    @foreach($division['items'] as $item)
                        <tr>
                            <td>{{$item['name']}}</td>
                            <td>
                                <div class="pull-right">
                                    <form action="{{ route('boq.destroy', $item) }}" method="post">
                                        <a href="{{route('boq.show', $item['id'])}}" class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i> Show
                                        </a>
                                        <a href="{{route('boq.edit', $item)}}" class="btn btn-xs btn-primary"
                                        <i class="fa fa-edit"></i> Edit
                                        </a>

                                        {{csrf_field()}} {{method_field('delete')}}
                                        <button class="btn btn-xs btn-warning"><i class="fa fa-trash-o"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


            </article>
        @endif
    </li>
@endforeach
