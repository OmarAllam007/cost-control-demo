@extends('layouts.app')
@section('header')
    <h2>Resources</h2>

    <div class="btn-toolbar pull-right">
        <a href="{{ route('resources.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add resource
        </a>

        <a href="{{ route('resources.import') }}" class="btn btn-sm btn-success">
            <i class="fa fa-cloud-upload"></i> Import
        </a>

        <a href="{{ route('resources.import-codes') }}" class="btn btn-sm btn-success">
            <i class="fa fa-cloud-upload"></i> Import Equivalent Codes
        </a>

        <a href="{{route('all-resources.modify')}}" class="btn btn-success btn-sm">
        <i class="fa fa-pencil" aria-hidden="true"></i>
        Modify
        </a>
        <a href="{{route('all_resources.export')}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete All</a>
        @endcan
    </div>
@stop

@section('body')
    @include('resources._filters')

    @if ($resources->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-2">Resource Code</th>
                <th class="col-xs-3">Resource Name</th>
                <th class="col-xs-2">Resource Type</th>
                <th class="col-xs-1">Rate</th>
                <th class="col-xs-1">Unit</th>
                <th class="col-xs-1">Waste</th>
                <th class="col-xs-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)
                <tr>
                    <td class="col-xs-2">{{ $resource->resource_code}}</td>
                    <td class="col-xs-3">{{ $resource->name}}</td>
                    <td class="col-xs-2">{{$resource->types->root->name or ''}}</td>
                    <td class="col-xs-1">{{ number_format($resource->rate, 2)}}</td>
                    <td class="col-xs-1">{{ $resource->units->type or ''}}</td>
                    <td class="col-xs-1">{{ number_format($resource->waste, 2)}} %</td>
                    <td class="col-xs-2">
                        <form action="{{ route('resources.destroy', $resource) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('resources.edit', $resource) }} ">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $resources->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No resources found</strong></div>
    @endif

    <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
        <form class="modal-dialog" action="{{route('resources.wipe')}}" method="post">
            {{csrf_field()}}
            {{method_field('delete')}}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete All Resources</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        Are you sure you want to delete all resources?

                        <input type="hidden" name="wipe" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete All</button>
                    <button type="button" class="btn btn-default"><i class="fa fa-close"></i> Cancel</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
    <script>
        (function (w, d, $) {
            $(function () {
                var typeModal = $('#ResourceTypeModal');
                var selectType = $('#selectType');

                var resetType = $('#resetType');

                var types = typeModal.find('.tree-radio');

                if (types.is(':checked')) {
                    selectType.after(resetType);
                }

                types.on('change', function () {
                    if (types.is(':checked')) {
                        resetType.show();
                    } else {
                        resetType.hide();
                    }
                }).change();


                resetType.on('click', function (e) {
                    e.preventDefault();
                    types.attr('checked', false);
                    resetType.hide();
                    selectType.text('Select Type');
                });
            });
        }(window, document, jQuery));
    </script>
@endsection