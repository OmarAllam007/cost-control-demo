<div class="modal fade resource-type"  tabindex="-1" role="dialog" id="ResourceTypeModal2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Resource Type</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach($resourcesTree as $division)
                        @include('resource-type._recursive_input', ['division' => $division, 'value' => $value])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>