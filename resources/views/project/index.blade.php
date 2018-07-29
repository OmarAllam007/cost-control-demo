@extends('home.master-data')
@section('header')

    <div class="display-flex">
        <h2 class="flex">Master Data &mdash; Projects</h2>
        <a href="{{ route('project.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add Project
        </a>
    </div>
@stop

@section('content')

    @if ($projectGroups->count())
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
                                    @can('modify', $project)
                                        <a class="btn btn-primary btn-sm" href="{{ route('project.edit', $project) }} "><i class="fa fa-edit"></i> Edit</a>
                                        <a class="btn btn-primary btn-sm" href="{{ route('project.duplicate', $project) }} "><i class="fa fa-copy"></i> Duplicate</a>
                                        <a class="btn btn-danger btn-sm delete-btn" href="{{route('project.destroy', $project)}}"  title="Delete - {{$project->name}}"><i class="fa fa-trash-o"></i> Delete</a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

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