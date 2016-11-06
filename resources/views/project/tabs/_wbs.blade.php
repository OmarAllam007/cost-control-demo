<form action="{{route('wbs-level.wipe', $project)}}" method="post" class="form-group tab-actions pull-right">
    {{csrf_field()}} {{method_field('delete')}}
    <a href="{{route('wbs-level.import', $project->id)}}" class="btn btn-success btn-sm">
        <i class="fa fa-cloud-upload"></i> Import
    </a>

    <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add Level
    </a>
    <a href="{{route('wbs-level.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
        <i class="fa fa-cloud-download"></i> Export
    </a>

    @can('wipe')
        <a href="#WipeWbsModal" data-toggle="modal" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete all</a>
        @endcan
</form>


<div class="clearfix"></div>
@if (count($wbsTree))
    <ul class="list-unstyled tree">
    @foreach($wbsTree as $wbs_level)
            @include('wbs-level._recursive', compact('wbs_level'))
        @endforeach
    </ul>

    <div class="modal fade" id="WipeWbsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <form method="post" action="{{route('wbs-level.wipe', $project)}}" class="modal-content">
                {{csrf_field()}} {{method_field('delete')}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Delete all WBS Levels</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all WBS Levels?</div>
                    <input type="hidden" name="wipe" value="1">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete all</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No WBS found</div>
@endif

