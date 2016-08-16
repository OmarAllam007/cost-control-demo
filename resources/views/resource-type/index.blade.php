@extends('layouts.app')

@section('header')
    <h2>Resource type</h2>
    <a href="{{ route('resource-type.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add resource_type</a>
@stop

@section('body')
    @if ($resourceTypes->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Resource Name</th>
                <th>Sub Division#1</th>
                <th>Sub Division#2</th>
                <th>Resource Name</th>

                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($resourceTypes as $resource_type)
                    <tr>
                        <td class="col-sm-1"><a href="{{ route('resource-type.edit', $resource_type) }}">{{
                        $resource_type->getParent($resource_type->id) }}</a></td>
                        <td class="col-md-1"><a href="{{ route('resource-type.edit', $resource_type) }}"></a>
                        {{$resource_type->getDivision($resource_type->parent_id)}}</td>
                        <td class="col-md-1"><a href="{{ route('resource-type.edit', $resource_type) }}">{{$resource_type->getDivisions($resource_type->parent_id)}}</a></td>
                        <td class="col-md-1"><a href="{{ route('resource-type.edit', $resource_type) }}"></a></td>
                        <td class="col-md-1">
                            <form action="{{ route('resource-type.destroy', $resource_type) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('resource-type.edit', $resource_type) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $resourceTypes->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No resource type found</strong></div>
    @endif
@stop
