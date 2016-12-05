<div class="form-group tab-actions pull-right">
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

    @can('wipe')
        <a href="WipeBoqModal" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete All</a>
    @endcan
</div>

<div class="clearfix"></div>

@if ($boqArray)
    <ul class="list-unstyled tree">
        @include('boq-division._recursive2')
    </ul>
@else

    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Boq found</div>
@endif

<div class="modal fade" id="WipeBoqModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form method="post" action="{{route('boq.wipe', $project)}}" class="modal-content">
            {{csrf_field()}} {{method_field('delete')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Delete all BOQ</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all BOQ in the project?</div>
                <input type="hidden" name="wipe" value="1">
                                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" v-on:click="wipeAll" :disabled="wiping">
                    <i class="fa fa-@{{ wiping? 'spinner fa-spin' : 'trash' }}"></i> Wipe
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
