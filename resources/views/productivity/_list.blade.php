<table class="table table-condensed table-striped">
    <thead>
    <tr>
        <th>Description</th>
        <th>Unit</th>
        <th>Crew Structure</th>
        <th>Daily Output</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productivities as $productivity)
        <tr>
            <td>{!! nl2br(e($productivity->description)) !!}</td>
            <td>{!! nl2br(e($productivity->crew_structure)) !!}</td>
            <td>{{ isset($productivity->units->type)?$productivity->units->type:'' }}</td>
            <td>{{number_format(floatval($productivity->daily_output), 2)}}</td>
            <td class="col-md-2">
                <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                    {{csrf_field()}} {{method_field('delete')}}
                    <a class="btn btn-sm btn-primary" href="{{route('productivity.edit', $productivity)}}">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>