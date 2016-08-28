@extends('layouts.app')
@section('header')
    <h2>Project</h2>
    <div >
    <a href="{{ route('project.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add
        project</a>

    <button href="" class="btn btn-sm btn-primary pull-right" id="upload_button"><i class="fa fa-upload"></i> Upload
        project
    </button>
    </div>


@stop

@section('body')

    <script>
        $(document).ready(function () {
            $("#upload_button").click(function () {
                $(".upload").toggle("fast", function () {
                });
            });
        });

    </script>
    <div class="form-group upload pull-right" style="display: none;">
        {!! Form::open(array('action' => 'ProjectController@upload', 'files' => true,'class'=>'form-inline')) !!}
        {!! Form::file('file',['class'=>'form-control']) !!}
        {!! Form::token() !!}
        {!! Form::submit('Upload',['class'=>'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
    @if ($projects->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th class="col-sm-8">Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($projects as $project)
                <tr>
                    <td><a href="{{ route('project.show', $project) }}">{{ $project->name }}</a></td>
                    <td>
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
