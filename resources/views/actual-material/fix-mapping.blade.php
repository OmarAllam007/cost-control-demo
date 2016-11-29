@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Material &mdash; Mapping</h2>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    <div class="row">
        @if ($mapping->count())
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

                target.text(code).siblings('input').val(id);

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

                var id = $(this).data('id');
                var code = $(this).data('code');
                var target = selectResourceModal.data('target');
console.log(code);
console.log(id);
                target.text(code).siblings('input').val(id);

                selectResourceModal.modal('hide');
            });

        });
    </script>
@endsection