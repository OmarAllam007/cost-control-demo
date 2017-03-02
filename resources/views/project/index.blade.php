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
    @if ($projectGroups->count())
        @foreach($projectGroups as $groupName => $projects)
            @if ($projects->count())
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title"><a href="#{{slug($groupName ?: 'not-assigned')}}" data-toggle="collapse">{{$groupName?: 'Not Assigned'}}</a>
                    </h4>
                </div>

                <table class="table table-condensed table-striped table-hover collapse" id="{{slug($groupName ?: 'not-assigned')}}" >
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
                                    <a class="btn btn-sm btn-info"
                                       href="{{ route('project.budget', $project) }}">Budget</a>
                                @else
                                    @can('reports', $project)
                                        <a class="btn btn-sm btn-info"
                                           href="{{ route('project.budget', $project) }}">Budget</a>
                                    @endcan
                                @endcan

                                @can('cost_control', $project)
                                    <a class="btn btn-sm btn-violet"
                                       href="{{ route('project.cost-control', $project) }}">Cost
                                        Control</a>
                                @else
                                    @can('reports', $project)
                                        <a class="btn btn-sm btn-violet"
                                           href="{{ route('project.cost-control', $project) }}">Cost
                                            Control</a>
                                    @endcan
                                @endcan

                                @can('modify', $project)
                                    <a class="btn btn-sm btn-primary" href="{{ route('project.edit', $project) }} "><i
                                                class="fa fa-edit"></i> Edit</a>
                                    <a class="btn btn-sm btn-default btn-outline"
                                       href="{{ route('project.duplicate', $project) }} "><i class="fa fa-copy"></i>
                                        Duplicate</a>
                                    <a href="{{route('project.destroy', $project)}}"
                                       class="btn btn-sm btn-warning delete-btn" title="Delete - {{$project->name}}"><i
                                                class="fa fa-trash-o"></i> Delete </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            @endif
        @endforeach

        @can('modify', $project)
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
        @endcan
    @endif
@stop

@section('javascript')
    @if ($projectGroups->count())
        <script>
            $(function () {
                var deleteModal = $('#DeleteProjectModal');
                var deleteForm = deleteModal.find('form');
                var title = deleteForm.find('.modal-title');
                var data = 0;

                $('.delete-btn').on('click', function (e) {
                    e.preventDefault();

                    title.text(this.title);
                    deleteForm.attr('action', this.href);

                    deleteModal.modal();
                })
            })
        </script>
    @endif
@endsection