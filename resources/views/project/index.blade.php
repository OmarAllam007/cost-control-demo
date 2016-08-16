@extends('layouts.app')

@section('header')
    <h2>Project</h2>
    <a href="{{ route('project.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add project</a>
@stop

@section('body')
    @if ($projects->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('project.edit', $project) }}">{{ $project->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('project.destroy', $project) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('project.edit', $project) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $projects->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No project found</strong></div>
    @endif
@stop