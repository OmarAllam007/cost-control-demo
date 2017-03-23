<div id="CSICategoryModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Category</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\CsiCategory::tree()->get()->sortBy('name') as $level)
                        @include('csi-category._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
