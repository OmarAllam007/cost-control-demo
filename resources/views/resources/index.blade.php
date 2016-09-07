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
    @if ($resources->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>

                <th>Resource Code</th>
                <th>Name</th>
                <th>Resource Type</th>
                <th>Rate</th>
                <th>Unit</th>
                <th>Waste</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)
                <tr>
                    <td class="col-md-1">{{ $resource->resource_code }}</td>
                    <td class="col-md-1">{{ $resource->name }}</td>
                    <td class="col-md-2">{{$resource->types->name or ''}}</td>
                    <td class="col-md-1">{{ $resource->rate }}</td>
                    <td class="col-md-1">{{ $resource->units->type or ''}}</td>
                    <td class="col-md-1">{{ $resource->waste }}</td>

                    <td class="col-md-2">
                        <form action="{{ route('resources.destroy', $resource) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('resources.edit', $resource) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
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
