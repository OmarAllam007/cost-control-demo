<div class="form-group">
    <label for="wbs">WBS</label>
    <a class="btn btn-default btn-block" href="#SelectWBSModal" data-toggle="modal">Select WBS</a>

</div>

<div class="modal fade" tabindex="-1" role="dialog" id="SelectWBSModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select WBS</h4>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                <ul class="list-unstyled tree">
                    @foreach((new \App\Support\WBSTree($project))->get() as $level)
                        @include('reports.partials.wbs-level', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
<script>
    $(function() {
        $('#SelectWBSModal').on('click', '.open-level', function(e) {
            e.preventDefault();
            $(this).closest('li').find('> ul').toggleClass('hidden')
        }).on('change', 'input:checkbox', function() {
            $(this).closest('li').find('input:checkbox').prop('checked', this.checked);
        });
    });
</script>
@append