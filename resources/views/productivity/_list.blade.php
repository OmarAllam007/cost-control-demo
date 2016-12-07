<table class="table table-condensed table-striped">
    <thead>
    <tr>
        <th class="col-xs-2">CSI CODE</th>
        <th class="col-xs-3">Description</th>
        <th class="col-xs-2">Crew Structure</th>
        <th class="col-xs-1">Unit</th>
        <th class="col-xs-1">Daily Output</th>
        <th class="col-xs-2">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productivities as $productivity)
        <tr>

            <td class="col-xs-2">{{ $productivity['csi_code']?:'' }}</td>
            <td class="col-xs-3">{!! nl2br(e($productivity['description']?:'')) !!}</td>
            <td class="col-xs-3">{!! nl2br(e($productivity['crew_structure']?:'')) !!}</td>
            <td class="col-xs-1">{{$productivity['unit'] }}</td>
            <td class="col-xs-1">{{number_format(floatval($productivity['daily_output']), 2)?:0}}</td>
            <td class="col-xs-2">
                <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                    {{csrf_field()}} {{method_field('delete')}}
                    <a class="btn btn-sm btn-primary" href="{{route('productivity.edit', $productivity)}}">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                    <a class="btn btn-sm btn-info" href="{{ route('productivity.show', $productivity) }} "><i class="fa fa-eye"></i>
                        View</a>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>