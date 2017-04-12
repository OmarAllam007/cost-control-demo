@extends('layouts.' . (request('iframe') ? 'iframe' : 'app'))

@section('header')
    <h3>{{$batch->project->name}} &mdash; {{$batch->period->name}}</h3>

    <div class="pull-right">
        <a href="{{route('project.cost-control', $batch->project)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
    @stop

@section('body')

    <div class="row">

        <div class="col-sm-4">
            <strong>Uploaded By: </strong>{{$batch->user->name}}
        </div>
        <div class="col-sm-4">
            <strong>Uploaded at: </strong>{{$batch->created_at->format('d/m/Y H:i')}}
        </div>
        <div class="col-sm-4">
            <strong><i class="fa fa-download"></i> <a href="{{'/actual-batches/' . $batch->id . '/download'}}">Download</a></strong>
        </div>
    </div>

    @can('cost_owner', $batch->project)
    <div class="form-group clearfix">
        <div class="pull-right">
            <a  href="#DeleteBatchModal" class="btn btn-danger" data-toggle="modal"><i class="fa fa-trash"></i> Delete</a>
        </div>
    </div>

    <div class="modal fade" id="DeleteBatchModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Delete Data Upload</h4>
                </div>
                <div class="modal-body">
                    <p class="lead text-danger">Are you sure you want to delete this data upload all related data?</p>
                    <p class="alert alert-danger hidden" id="delete-warning">Could not delete upload</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="DeleteBtn"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <h3 class="page-header">Issues</h3>

    @forelse($batch->issues as $issue)
        @include('actual-batches.issues.' . $issue->type, compact('issue'))
    @empty
        <div class="alert alert-info">No issues found on this upload</div>
    @endforelse

@stop

@section('javascript')
    @can('cost_owner', $batch->project)
        <script>
            $(function() {
                $('#DeleteBtn').on('click', function () {
                    var _this = $(this);
                    _this.attr('disabled', true).find('.fa').toggleClass('fa-trash fa-spinner fa-spin');
                    $('#delete-warning').addClass('hidden');
                    $.ajax({
                        url: '/api/cost/delete-batch/{{$batch->id}}',
                        dataType: 'json', 'method': 'delete', data: { _token: $('meta[name=csrf-token]').attr('content')}
                    }).success(function(response) {
                        _this.attr('disabled', false).find('.fa').toggleClass('fa-trash fa-spinner fa-spin');
                        window.parent.$('.modal.in').modal('hide');
                        window.parent.app.reload('data_uploads', {type: 'info', message: 'Data has been deleted'});
                    }).error(function(error) {
                        _this.attr('disabled', false).find('.fa').toggleClass('fa-trash fa-spinner fa-spin');
                        $('#delete-warning').removeClass('hidden');
                    });
                });
            });
        </script>
    @endcan
@endsection