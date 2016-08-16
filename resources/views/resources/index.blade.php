@extends('layouts.app')

@section('header')
    <h2>Resources</h2>
    <a href="{{ route('resources.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add
        resource</a>
@stop

@section('body')
    @if ($resources->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>

                <th>Resource Code</th>
                <th>Name</th>
                <th>Rate</th>
                <th>Unit</th>
                <th>Waste</th>
                <th>Business Partner</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)
                <tr>
                    <td class="col-md-1"><a
                                href="{{ route('resources.edit', $resource) }}">{{ $resource->resource_code }}</a></td>

                    <td class="col-md-1"><a href="{{ route('resources.edit', $resource) }}">{{ $resource->name }}</a>
                    </td>
                    <td class="col-md-1"><a href="{{ route('resources.edit', $resource) }}">{{ $resource->rate }}</a>
                    </td>
                    <td class="col-md-1"><a href="{{ route('resources.edit', $resource) }}">{{ $resource->unit }}</a>
                    </td>
                    <td class="col-md-1"><a href="{{ route('resources.edit', $resource) }}">{{ $resource->waste }}</a>
                    </td>
                    <td class="col-md-2"><a href="{{ route('resources.edit', $resource) }}">
                            {{$resource->businessParteners->name}}
                        </a></td>

                    <td class="col-md-3">
                        <form action="{{ route('resources.destroy', $resource) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('resources.edit', $resource->id) }} "><i
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
