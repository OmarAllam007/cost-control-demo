<div class="form-group">
    <label for="wbs">Activity</label>
    <a class="btn btn-default btn-block" href="#SelectActivityModal" data-toggle="modal">Select Activity</a>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="SelectActivityModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Activity</h4>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                <ul class="list-unstyled tree">
                    @foreach((new \App\Support\StdActivityTree())->get() as $division)
                        @include('reports.partials.activity-level', compact('division'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script>
        $(function() {
            $('#SelectActivityModal').on('click', '.open-level', function(e) {
                e.preventDefault();
                $(this).closest('li').find('> ul').toggleClass('hidden')
            }).on('change', 'input:checkbox', function() {
                $(this).closest('li').find('input:checkbox').prop('checked', this.checked);
            });
        });
    </script>
@append