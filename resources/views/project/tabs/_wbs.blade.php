<div class="form-group tab-actions clearfix">
    <a href="{{route('survey.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
        <i class="fa fa-plus"></i> Add Quantity Survey
    </a>
</div>

@if ($project->wbs_levels->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>

        </tr>
        </thead>
        <tbody>
        @foreach($project->wbs_levels as $level)
            <tr>
                <td>{{$level->name}}</td>

                <td>
                    {{Form::open(['route' => ['survey.destroy', $level], 'method' => 'delete'])}}
                    <a href="{{route('survey.edit', $level)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash"></i> Delete</button>
                    {{Form::close()}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info"><i class="fa fa-info-circle"></i> No Quantity Survey</div>
@endif