@extends('layouts.app')
@section('header')
    <h2>Projects</h2>
    <a href="{{ route('project.create') }} " class="btn btn-sm btn-primary pull-right">
        <i class="fa fa-plus"></i> Add Project
    </a>
@stop

@section('body')


    @if ($projects->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-8">Name</th>
                <th class="col-xs-4">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($projects as $project)
                <tr>
                    <td class="col-xs-8"><a href="{{ route('project.show', $project) }}">{{ $project->name }}</a></td>
                    <td class="col-xs-4">
                        <form action="{{ route('project.destroy', $project) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-info" href="{{ route('project.show', $project) }} "><i
                                        class="fa fa-edit"></i> Show</a>
                            <a class="btn btn-sm btn-primary" href="{{ route('project.edit', $project) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
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
