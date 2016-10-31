
<div id="ResourceTypeModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Resource Type</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ResourceType::tree()->get() as $division)
                        @include('resource-type._recursive_input', ['division' => $division, 'value' => $value])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>