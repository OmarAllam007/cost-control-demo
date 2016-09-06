<div class="form-group tab-actions clearfix">
    <div class="pull-right">
        <a href="{{route('survey.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Quantity Survey
        </a>

        <a href="{{route('survey.import', ['project' => $project->id])}}" class="btn btn-success btn-sm">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
    </div>
</div>

@if ($project->quantities->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Cost Account</th>
            <th>WBS</th>
            <th>Description</th>
            <th>Budget Quantity</th>
            <th>Eng Quantity</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @foreach($project->quantities as $quantity)
            <tr>
                <td>{{$quantity->cost_account}}</td>
                <td>{{$quantity->wbsLevel->code or ''}}</td>
                <td class="col-md-4">{{$quantity->description}}</td>
                <td>{{$quantity->budget_qty}}</td>
                <td>{{$quantity->eng_qty}}</td>
                <td class="col-md-2">
                    {{Form::open(['route' => ['survey.destroy', $quantity], 'method' => 'delete'])}}
                    <a href="{{route('survey.edit', $quantity)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash"></i> Delete</button>
                    {{Form::close()}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Quantity Survey</div>
@endif