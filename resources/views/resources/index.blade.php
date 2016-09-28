@extends('layouts.app')
@section('header')
    <h2>Resources</h2>

    <div class="btn-toolbar pull-right">
        <a href="{{ route('resources.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add resource
        </a>

        <a href="{{ route('resources.import') }} " class="btn btn-sm btn-success">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
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
                    <td class="col-xs-2">{{ $resource->resource_code }}</td>
                    <td class="col-xs-3">{{ $resource->name }}</td>
                    <td class="col-xs-2">{{$resource->types->root->name or ''}}</td>
                    <td class="col-xs-1">{{ number_format($resource->rate, 2) }}</td>
                    <td class="col-xs-1">{{ $resource->units->type or ''}}</td>
                    <td class="col-xs-1">{{ number_format($resource->waste, 2) }} %</td>
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