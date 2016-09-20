@extends('layouts.app')

@section('header')
    <h2>Standard Activity</h2>
    <div class="pull-right">
        <a href="{{ route('std-activity.create') }} " class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Activity</a>
        <a href="{{ route('std-activity.import') }} " class="btn btn-sm btn-success"><i class="fa fa-cloud-upload"></i> Import</a>
    </div>
@stop

@section('body')
    @if ($stdActivities->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-4">Division</th>
                <th class="col-xs-4">Name</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($stdActivities as $std_activity)
                    <tr>
                        <td class="col-xs-4">{{ $std_activity->division->path }}</td>
                        <td class="col-xs-4"><a href="{{ route('std-activity.edit', $std_activity) }}">{{ $std_activity->name }}</a></td>
                        <td class="col-xs-4">
                            <form action="{{ route('std-activity.destroy', $std_activity) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-info" href="{{ route('std-activity.show', $std_activity) }} "><i class="fa fa-eye"></i> View</a>
                                <a class="btn btn-sm btn-primary" href="{{ route('std-activity.edit', $std_activity) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $stdActivities->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No std activity found</strong></div>
    @endif
@stop
