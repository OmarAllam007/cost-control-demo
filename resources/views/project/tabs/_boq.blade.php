<div class="form-group tab-actions pull-right">
    <form action="{{ route('boq.delete-all', $project) }}" method="post">
        {{csrf_field()}}  {{method_field('delete')}}
    <a href="{{route('boq.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add item
    </a>

    <a href="{{route('boq-division.index')}}" class="btn btn-primary btn-sm">Manage Divisions</a>

    <a href="{{route('boq.import', $project->id)}}" class="btn btn-success btn-sm">
        <i class="fa fa-cloud-upload"></i> Import
    </a>

    <a href="{{route('boq.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
        <i class="fa fa-cloud-download"></i> Export
    </a>

        <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete All</button>
    </form>

</div>

<div class="clearfix"></div>

@if ($divisions)
    <ul class="list-unstyled tree">
            @include('boq-division._recursive2', compact('division'))
    </ul>
@else

    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Boq found</div>
@endif


