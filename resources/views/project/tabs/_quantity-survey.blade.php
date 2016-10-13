<div class="form-group tab-actions clearfix">
    <div class="pull-right">
        <a href="{{route('survey.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Quantity Survey
        </a>

        <a href="{{route('survey.import', ['project' => $project->id])}}" class="btn btn-success btn-sm">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
        <a href="{{route('survey.post-export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
    </div>
</div>

@if ($project->quantities->count())
    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Cost Account</th>
            <th class="col-xs-2">WBS</th>
            <th class="col-xs-2">Description</th>
            <th class="col-xs-2">Budget Quantity</th>
            <th class="col-xs-2">Eng Quantity</th>
            <th class="col-xs-2">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($project->quantities as $quantity)
            <tr>
                <td class="col-xs-2">{{$quantity->cost_account}}</td>
                <td class="col-xs-2"><abbr title="{{$quantity->wbsLevel->path}}">{{$quantity->wbsLevel->code or ''}}</abbr></td>
                <td class="col-xs-2">{{$quantity->description}}</td>
                <td class="col-xs-2">{{$quantity->budget_qty}}</td>
                <td class="col-xs-2">{{$quantity->eng_qty}}</td>
                <td class="col-xs-2">
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