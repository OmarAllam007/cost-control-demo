@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Fix Import Activity Map</h2>

    <div class="pull-right">
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i
                    class="fa fa-chevron-left"></i> Back to project</a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-9">

            {{Form::open()}}

            <table class="table table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <th>Activity Code</th>
                    <th>Store Activity</th>
                    <th>Selected Activity</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{$row[0]}}</td>
                        <td>{{$row[1]}}</td>
                        <td>
                            <a href="#ActivityModal"
                               class="select-activity">{{old("mapping[{$row[1]}]") ?: "Select Activity"}}</a>
                            {{Form::hidden("mapping[{$row[1]}]", null)}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="SelectActivityModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Select Activity</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="search" id="SearchActivity" class="form-control"
                               placeholder="Please type to search">
                    </div>

                    <table class="table table-striped table-condensed table-hover table-fixed">
                        <thead>
                        <tr>
                            <th class="col-sm-3">Code</th>
                            <th class="col-sm-6">WBS</th>
                            <th class="col-sm-3">Activity</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($codes as $activity)
                            <tr>
                                <td class="col-sm-3"><a href="#" class="select-code">{{$activity->code}}</a></td>
                                <td class="col-sm-6">{{$activity->wbs->path}}</td>
                                <td class="col-sm-3">{{$activity->activity}}</td>
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
            var modal = $('#SelectActivityModal').on('shown.bs.modal', function () {
                $(this).find('input').focus();
            });

            var table = modal.find('table');

            $('.select-activity').on('click', function (e) {
                e.preventDefault();

                modal.data('target', this);
                modal.modal();
            });

            $('#SearchActivity').on('keyup', function () {
                var val = $.trim(this.value.toLowerCase());
                if (val) {
                    table.find('tbody tr').each(function () {
                        var tr = $(this);
                        var show = false;
                        tr.find('td').each(function() {
                            if ($(this).text().toLowerCase().indexOf(val) != -1) {
                                show = true;
                                return true;
                            }
                        });

                        show? tr.show() : tr.hide();
                    });
                } else {
                    table.find('tbody tr').show();
                }
            });

            modal.find('.select-code').on('click', function(e) {
                e.preventDefault();
                var code = $(this).text();
                var target = $(modal.data('target'));
                target.siblings('input').val(code);
                target.text(code);
                modal.modal('hide');
            });
        });
    </script>
@endsection