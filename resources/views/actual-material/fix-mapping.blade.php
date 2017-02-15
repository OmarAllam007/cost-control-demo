@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; Mapping Issues</h4>
        <h4 class="pull-right text-muted">#E01</h4>
    </div>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    <div class="row">
        @if ($activity->count())
            @include('actual-material._activity-mapping')
        @endif

        @if ($resources->count())
            @include('actual-material._resource-mapping')
        @endif
    </div>


    <div class="form-group">
        <button class="btn btn-success">Next <i class="fa fa-chevron-circle-right"></i></button>
    </div>

    {{Form::close()}}




@endsection

@section('javascript')
    <script>
        $(function () {
            var selectActivityModal = $('#SelectActivityModal').on('shown.bs.modal', function () {
                activitySearch.focus();
            });

            var activitySearch = $('#activity-search').on('keyup', function () {
                var value = this.value;
                if (value) {
                    var regExp = new RegExp(value, 'i');
                    selectActivityModal.find('tbody tr').each(function (idx, row) {
                        var _row = $(row);
                        var code = _row.find('.code').text();
                        var cost_account = _row.find('.cost-control').text();
                        if (regExp.test(code) || regExp.test(cost_account)) {
                            _row.show();
                        } else {
                            _row.hide();
                        }
                    });
                } else {
                    selectActivityModal.find('tbody tr').show();
                }
            });

            $('.select-activity-trigger').on('click', function (e) {
                e.preventDefault();
                selectActivityModal.data('target', $(this)).modal();
            });

            $('.select-activity').on('click', function (e) {
                e.preventDefault();

                var id = $(this).data('id');
                var code = $(this).data('code');
                var target = selectActivityModal.data('target');

                target.text(code).siblings('input').val(code);

                selectActivityModal.modal('hide');
            });

            var selectResourceModal = $('#SelectResourceModal').on('shown.bs.modal', function () {
                resourcesSearch.focus();
            });

            var resourcesSearch = $('#ResourcesSearch').on('keyup', function () {
                var value = this.value;
                if (value) {
                    var regExp = new RegExp(value, 'i');
                    selectResourceModal.find('tbody tr').each(function (idx, row) {
                        var _row = $(row);
                        var code = _row.find('.code').text();
                        var name = _row.find('.name').text();
                        if (regExp.test(code) || regExp.test(name)) {
                            _row.show();
                        } else {
                            _row.hide();
                        }
                    });
                } else {
                    selectResourceModal.find('tbody tr').show();
                }
            });

            $('.select-resource-trigger').on('click', function (e) {
                e.preventDefault();
                selectResourceModal.data('target', $(this)).modal();
            });

            $('.select-resource').on('click', function (e) {
                e.preventDefault();

                var code = $(this).data('code');
                var target = selectResourceModal.data('target');

                target.text(code).siblings('input').val(code);

                selectResourceModal.modal('hide');
            });

            $('.skip-all').on('change', function(){
                $(this).closest('table').find(':checkbox').prop('checked', this.checked);
            });
        });
    </script>
@endsection