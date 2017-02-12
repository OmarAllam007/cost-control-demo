@extends('layouts.app')

@section('header')
    <h2>
        @if ($project)
        {{$project->name}}
        @endif
        &mdash; Fix Import Resource Map</h2>

    <div class="pull-right">
        @if ($project)
            <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back to project</a>
        @else
            <a href="{{route('resources.index')}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back to resources</a>
        @endif
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-9">

            {{Form::open()}}

            <table class="table table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <th>Resource Code</th>
                    <th>Store Resource Code</th>
                    <th>Selected Resource</th>
                </tr>
                </thead>
                <tbody>
                @foreach($failed as $row)
                    <tr>
                        <td>{{$row[0]}}</td>
                        <td>{{$row[1]}}</td>
                        <td>
                            <a href="#SelectResourcesModal" class="select-resource">{{old("mapping[{$row[1]}]") ?: "Select Resource"}}</a>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="SelectResourcesModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Select Activity</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="search" id="SearchResource" class="form-control"
                               placeholder="Please type to search">
                    </div>

                    <table class="table table-striped table-condensed table-hover table-fixed">
                        <thead>
                        <tr>
                            <th class="col-sm-3">Code</th>
                            <th class="col-sm-6">Resource</th>
                            <th class="col-sm-3">Resource Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($resources as $resource)
                            <tr>
                                <td class="col-sm-3"><a href="#" class="select-code" data-id="{{$resource->id}}">{{$resource->resource_code}}</a></td>
                                <td class="col-sm-6">{{$resource->name}}</td>
                                <td class="col-sm-3">{{$resource->types->root->name ?? ''}}</td>
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
            var modal = $('#SelectResourcesModal').on('shown.bs.modal', function () {
                $(this).find('input').focus();
            });

            var table = modal.find('table');

            $('.select-resource').on('click', function (e) {
                e.preventDefault();

                modal.data('target', this);
                modal.modal();
            });

            $('#SearchResource').on('keyup', function () {
                var val = $.trim(this.value.toLowerCase());
                if (val) {
                    table.find('tbody tr').each(function () {
                        var tr = $(this);
                        var show = false;
                        tr.find('td').each(function () {
                            if ($(this).text().toLowerCase().indexOf(val) !== -1) {
                                show = true;
                                return true;
                            }
                        });

                        show ? tr.show() : tr.hide();
                    });
                } else {
                    table.find('tbody tr').show();
                }
            });

            modal.find('.select-code').on('click', function (e) {
                e.preventDefault();
                var code = $(this).data('id');
                var text = $(this).text();
                var target = $(modal.data('target'));
                target.siblings('input').val(code);
                target.text(text);
                modal.modal('hide');
            });
        });
    </script>
@endsection