<div class="modal fade" id="concerns-modal" tabindex="-1" role="dialog">
    <form action="{{route('concerns.store', $project)}}" method="post" class="modal-dialog modal-lg">
        <div class="modal-content">
            {{csrf_field()}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Add Concern</h4>
            </div>

            <div class="modal-body">
                <input type="hidden" name="period_id" value="{{$period->id}}" id="concern-period">
                <input type="hidden" name="report_name" value="{{$report_name}}">

                <h4>
                    {{$project->name}} &mdash; {{$period->name}}
                    @if (isset($report_name))
                        / {{$report_name}}
                    @endif
                </h4>

                <section class="scrollpane" style="margin-bottom: 2rem">
                    <table class="table table-bordered table-striped" style="width: auto;">
                        <thead>
                        <tr></tr>
                        </thead>
                        <tbody>
                        <tr></tr>
                        </tbody>
                    </table>
                </section>

                <div class="form-group">
                    <label for="comment" class="control-label">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" cols="30" rows="7"></textarea>
                    <input type="hidden" name="data" value="" id="concern-data">
                </div>
            </div>

            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <button class="btn btn-primary send-concern">
                        <i class="fa fa-check"></i> Add Concern
                    </button>
                    <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        const concernsModal = $('#concerns-modal').on('bs.modal-shown', function() {
            $(this).find('textarea').focus();
        }).on('click', '.send-concern', function(e) {
            e.preventDefault();
            $(this).find('i').removeClass('fa-check').addClass('fa-spinner fa-spin').end().prop('disabled', true);
            $.ajax({
                url: concernsForm.attr('action'),
                data: concernsForm.serialize(),
                dataType: 'json',
                method: 'post'
            }).then(() => {
                concernsModal.modal('hide');
                concernsForm.find('textarea').val('');
                $(this).find('i').addClass('fa-check').removeClass('fa-spinner fa-spin').end().prop('disabled', false);
            }, () => {
                $(this).find('i').addClass('fa-check').removeClass('fa-spinner fa-spin').end().prop('disabled', false);
                // concernsModal.modal('hide');
            });
        });

        const concernsForm = concernsModal.find('form');

        const dataField = concernsModal.find('#concern-data');

        $('.concern-btn').on('click', function(e) {
            e.preventDefault();
            dataField.val(e.currentTarget.dataset.data);
            const data = JSON.parse(e.currentTarget.dataset.data);
            const header = concernsModal.find('thead tr');
            const body = concernsModal.find('tbody tr');

            header.find('th').remove();
            body.find('td').remove();

            for (const key in data) {
                const value = data[key];
                header.append($('<th>').text(key));
                body.append($('<td>').text(value));
            }

            concernsModal.modal();
        });
    });
</script>