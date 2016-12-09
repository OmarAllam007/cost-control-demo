@extends('layouts.app')
@section('header')
    <h2>Projects</h2>
    @if (Auth::user()->is_admin)
    <a href="{{ route('project.create') }} " class="btn btn-sm btn-primary pull-right">
        <i class="fa fa-plus"></i> Add Project
    </a>
    @endif
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
                    <td class="col-xs-8">
                        {{ $project->name }}
                    </td>
                    <td class="col-xs-4">
                        <form action="{{ route('project.destroy', $project) }}" method="post">
                            {{--@can('budget', $project)--}}
                                {{--<a class="btn btn-sm btn-info" href="{{ route('project.budget', $project) }}">Budget</a>--}}

                            {{--@endcan--}}
                            {{--@can('cost_control')--}}
                                {{--<a class="btn btn-sm btn-info" href="{{ route('project.cost-control', $project) }}">Cost Control</a>--}}
                            {{--@endcan--}}

                            @can('reports')
                            <a class="btn btn-sm btn-info" href="{{ route('project.budget', $project) }}">Budget</a>
                            <a class="btn btn-sm btn-info" href="{{ route('project.cost-control', $project) }}">Cost Control</a>
                            @endcan

                            @can('modify')
                                <a class="btn btn-sm btn-primary" href="{{ route('project.edit', $project) }} "><i class="fa fa-edit"></i> Edit</a>
                                {{csrf_field()}} {{method_field('delete')}}
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            @endcan
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
