<table class="table table-condensed table-striped table-fixed">
    <thead>
    <tr>
        <th class="col-xs-1">CSI CODE</th>
        <th class="col-xs-3">Description</th>
        <th class="col-xs-3">Crew Structure</th>
        <th class="col-xs-1">Unit</th>
        <th class="col-xs-2">Daily Output</th>
        <th class="col-xs-2">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productivities as $productivity)

        <tr>
            <td class="col-xs-1">{{ $productivity->category->code }}</td>
            <td class="col-xs-3">{!! nl2br(e($productivity->description)) !!}</td>
            <td class="col-xs-3">{!! nl2br(e($productivity->crew_structure)) !!}</td>
            <td class="col-xs-1">{{ isset($productivity->units->type)?$productivity->units->type:'' }}</td>
            <td class="col-xs-2">{{number_format(floatval($productivity->daily_output), 2)}}</td>
            <td class="col-xs-2">
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