<div class="form-group tab-actions clearfix">
    <div class="pull-right">
        <a href="{{route('survey.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Quantity Survey
        </a>

        <a href="{{route('survey.import', ['project' => $project->id])}}" class="btn btn-success btn-sm">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
        <a href="{{route('survey.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>

        @can('wipe')
            <a href="#WipeQSModal" data-toggle="modal" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete all</a>
        @endcan
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
                <td class="col-xs-2"><abbr title="">{{$quantity->wbsLevel->code or ''}}</abbr></td>
                <td class="col-xs-2">{{$quantity->description}}</td>
                <td class="col-xs-2">{{$quantity->budget_qty}}</td>
                <td class="col-xs-2">{{$quantity->eng_qty}}</td>
                <td class="col-xs-2"> 
                    {{Form::open(['route' => ['survey.destroy', $quantity], 'method' => 'delete' ,'class'=>'delete_form','data-name'=>'QS'])}}
                    <a href="{{route('survey.edit', $quantity)}}" class="btn btn-sm btn-primary"><i
                                class="fa fa-edit"></i> Edit</a>
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

<div class="modal fade" id="WipeQSModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form method="post" action="{{route('survey.wipe', $project)}}" class="modal-content">
            {{csrf_field()}} {{method_field('delete')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Delete all quantities</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all quantities?</div>
                <input type="hidden" name="wipe" value="1">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete all</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>