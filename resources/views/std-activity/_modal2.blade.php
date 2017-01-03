<div id="ActivitiesModal2" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Activity</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ActivityDivision::with('activities')->tree()->get() as $division)
                        @include('std-activity._recursive_activity_input', ['division' => $division, 'input' => isset($input)? $input : 'std_activity_id'])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
