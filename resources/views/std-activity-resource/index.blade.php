@extends('layouts.app')

@section('header')
    <h2>Std activity resource</h2>
    <a href="{{ route('std-activity-resource.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add std_activity_resource</a>
@stop

@section('body')
    @if ($stdActivityResources->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($stdActivityResources->sortBy('name') as $std_activity_resource)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('std-activity-resource.edit', $std_activity_resource) }}">{{ $std_activity_resource->name
                        }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('std-activity-resource.destroy', $std_activity_resource) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('std-activity-resource.edit', $std_activity_resource) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $stdActivityResources->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No std activity resource found</strong></div>
    @endif
@stop
