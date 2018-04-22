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

        <div class="row">
            <div class=" col-sm-8">
                @foreach($projectGroups as $groupName => $projects)
                    @if ($projects->count())
                        <article class="card">


                            <h3 class="card-title">
                                <a href="#{{slug($groupName ?: 'not-assigned')}}"
                                   data-toggle="collapse">{{$groupName?: 'Not Assigned'}}</a>
                            </h3>

                            <div class="card-body collapse" id="{{slug($groupName ?: 'not-assigned')}}">
                                @foreach($projects as $project)
                                    <div class="card-row display-flex">
                                        <h4 class="flex">{{$project->name}}</h4>

                                        <div class="">
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
                                                <div class="dropdown" style="display: inline-block">
                                                    <button class="btn btn-default btn-sm dropdown-toggle" data-target="{{slug($groupName ?: 'not-assigned')}}-menu"
                                                            data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu" id="{{slug($groupName ?: 'not-assigned')}}-menu">
                                                        <li><a href="{{ route('project.edit', $project) }} "><i
                                                                        class="fa fa-edit"></i> Edit</a></li>
                                                        <li><a
                                                               href="{{ route('project.duplicate', $project) }} "><i
                                                                        class="fa fa-copy"></i>
                                                                Duplicate</a></li>
                                                        <li><a href="{{route('project.destroy', $project)}}" class="delete-btn text-danger"
                                                               title="Delete - {{$project->name}}"><i class="fa fa-trash-o"></i> <span class="text-danger">Delete </span></a></li>
                                                    </ul>
                                                </div>



                                            @endcan
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </article>

                    @endif

                @endforeach
            </div>
            
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{asset('images/logo.png')}}" alt="logo">
                    </div>
                </div>
            </div>
        </div>


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