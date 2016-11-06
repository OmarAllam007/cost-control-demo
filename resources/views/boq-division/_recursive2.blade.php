@foreach($boqArray as $index => $division)
        <li>
            <div class="tree--item">
                <a href="#children-{{$index}}" class="tree--item--label" data-toggle="collapse">
                    <i class="fa fa-chevron-circle-right"></i> {{$division['name']}}
                </a>
            </div>
            <article id="children-{{$index}}" class="tree--child collapse">
                <table class="table table-striped table-hover table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th class="col-md-5">BOQ Item</th>
                        <th class="col-md-3">Cost Account</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($division['items'] as $item)
                        <tr>
                            <td>{{$item['description']}}</td>
                            <td>{{$item['cost_account']}}</td>
                            <td>
                                <form action="{{ route('boq.destroy', $item) }}" method="post">
                                    {{csrf_field()}} {{method_field('delete')}}

                                    <a href="{{route('boq.edit', $item)}}" class="btn btn-xs btn-primary">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <button class="btn btn-xs btn-warning"><i class="fa fa-trash-o"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </article>
        </li>
@endforeach
