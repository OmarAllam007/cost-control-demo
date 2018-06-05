<div class="modal" id="concerns-modal" tabindex="-1" role="dialog">
    <form action="{{route('concerns.store', $project)}}" method="post" class="modal-dialog modal-lg">
        <div class="modal-content">
            {{csrf_field()}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Add Concern</h4>
            </div>

            <div class="modal-body">
                <input type="hidden" name="period_id" value="" id="concern-period">
                <input type="hidden" name="report" value="{{$report_name}}">

                <h4>
                    {{$project->name}} &mdash; {{$period->name}}
                    @if (isset($report_name))
                        / {{$report_name}}
                    @endif
                </h4>

                <div class="form-group">
                    <label for="comment" class="control-label">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" cols="30" rows="7"></textarea>
                    <input type="hidden" name="data" value="" id="concern-data">
                </div>
            </div>

            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <button class="btn btn-primary send-concern" data-dismiss="modal">
                        <i class="fa fa-check"></i> Add Concern
                    </button>
                    <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>