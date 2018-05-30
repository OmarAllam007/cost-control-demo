<div class="modal" id="ConcernModal" tabindex="-1" role="dialog">
    <form action="{{route('concerns.store', $project)}}" method="post" class="modal-dialog">
        <div class="modal-content">
            {{csrf_field()}} {{method_field('post')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Add Concern</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="comment" class="control-label">Comment:</label>
                    <textarea name="comment" id="comment" class="form-control" cols="30" rows="5"></textarea>
                </div>
            </div>

            <input type="hidden" name="data" value="" id="concern-data">
            <input type="hidden" name="period_id" value="" id="concern-period">

            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal" id="ApplyConcern">
                    <i class="fa fa-check"></i> Add Concern
                </button>
            </div>

        </div>
    </form>
</div>