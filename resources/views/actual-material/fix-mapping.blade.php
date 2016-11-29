@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Material &mdash; Mapping</h2>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-striped table-hover table-condensed table-fixed">
                <thead>
                <tr>
                    <th class="col-sm-2">
                        <label>
                            {{Form::checkbox('skip_all', 1, null, ['id' => 'skip-all'])}} Skip
                        </label>
                    </th>
                    <th class="col-sm-5">Original Activity Code</th>
                    <th class="col-sm-5">Activity ID</th>
                </tr>
                </thead>
                <tbody>
                @foreach($mapping as $activity)
                    <tr>
                        <td class="col-sm-2">
                            <label>
                                {{Form::checkbox("skip[{$activity[3]}][skip]")}}
                            </label>
                        </td>
                        <td class="col-sm-5">{{$activity[3]}}</td>
                        <td class="col-sm-5">
                            <a href="#" class="select-activity-trigger">
                                Select Activity
                            </a>
                            {{Form::hidden("skip[{$activity[3]}][resource_id]", null, ['class' => 'resource_id'])}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <div class="form-group">
        <button class="btn btn-success">Next <i class="fa fa-chevron-circle-right"></i></button>
    </div>

    {{Form::close()}}


    <div class="modal fade" tabindex="-1" role="dialog" data-target="" id="SelectActivityModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Select Activity</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group form-group-sm">
                        <input type="search"
                               id="activity-search"
                               placeholder="Type here to search for code or cost account"
                               class="form-control"
                        >
                    </div>

                    <table class="table table-striped table-condensed table-fixed">
                        <thead>
                        <tr>
                            <th class="col-sm-2">Code</th>
                            <th class="col-sm-4">WBS</th>
                            <th class="col-sm-3">Activity</th>
                            <th class="col-sm-3">Cost Account</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($projectActivityCodes as $code)
                            <tr>
                                <td class="col-sm-2">
                                    <a href="#" data-dismiss="modal" data-code="{{$code->code}}"
                                       class="select-activity code" data-id="{{$code->id}}">
                                        {{$code->code}}
                                    </a>
                                </td>
                                <td class="col-sm-4">{{$code->breakdown->wbs_level->name}}</td>
                                <td class="col-sm-3">{{$code->breakdown->std_activity->name}}</td>
                                <td class="col-sm-3">
                                    <a href="#" data-dismiss="modal" data-code="{{$code->code}}"
                                       class="select-activity cost-account" data-id="{{$code->id}}">
                                        {{$code->cost_account}}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

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
                    selectActivityModal.find('tr').each(function (idx, row) {
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
                    selectActivityModal.find('tr').show();
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
        });
    </script>
@endsection