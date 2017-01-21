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
            @foreach($projects->sortBy('name') as $project)
                <tr>
                    <td class="col-xs-8">
                        {{ $project->name }}
                    </td>
                    <td class="col-xs-4">

                        @can('budget', $project)
                            <a class="btn btn-sm btn-info" href="{{ route('project.budget', $project) }}">Budget</a>
                        @else
                            @can('reports', $project)
                                <a class="btn btn-sm btn-info"
                                   href="{{ route('project.budget', $project) }}">Budget</a>
                            @endcan
                        @endcan

                        @can('cost_control', $project)
                            <a class="btn btn-sm btn-violet" href="{{ route('project.cost-control', $project) }}">Cost
                                Control</a>
                        @else
                            @can('reports', $project)
                                <a class="btn btn-sm btn-violet" href="{{ route('project.cost-control', $project) }}">Cost
                                    Control</a>
                            @endcan
                        @endcan

                        @can('modify', $project)
                            <a class="btn btn-sm btn-primary" href="{{ route('project.edit', $project) }} "><i class="fa fa-edit"></i> Edit</a>
                            <a class="btn btn-sm btn-default btn-outline" href="{{ route('project.duplicate', $project) }} "><i class="fa fa-copy"></i> Duplicate</a>
                            <a href="{{route('project.destroy', $project)}}" class="btn btn-sm btn-warning delete-btn" title="Delete - {{$project->name}}"><i class="fa fa-trash-o"></i> Delete </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $projects->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No project found</strong></div>
    @endif


    <div class="modal fade" id="DeleteProjectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <form action="" method="post" class="modal-content">
                {{csrf_field()}} {{method_field('delete')}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        Are you sure you want to delete this project?
                    </div>
                    <input type="hidden" name="wipe" value="1">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger"><i class="fa fa-fw fa-trash"></i> Delete</button>
                </div>
            </form>
        </div>
    </div>

@stop


@section('javascript')
    <script>
        $(function () {
            var deleteModal = $('#DeleteProjectModal');
            var deleteForm = deleteModal.find('form');
            var title = deleteForm.find('.modal-title');

            $('.delete-btn').on('click', function (e) {
                e.preventDefault();

                title.text(this.title);
                deleteForm.attr('action', this.href);

                deleteModal.modal();
            })
        })
    </script>
@endsection