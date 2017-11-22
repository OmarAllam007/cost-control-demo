@extends('layouts.' . (request('iframe') ? 'iframe' : 'app'))

@section('header')
    <div class="display-flex">
        <h2>Copy WBS to projects &mdash; {{$wbs_level->path}}</h2>

        <a class="btn btn-default btn-sm" href="{{route('project.budget', $wbs_level->project)}}">
            <i class="fa fa-chevron-left"></i> Back to project
        </a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-6">

            {{Form::open(['id' => 'copyWbsForm', 'url' => request()->fullUrl()])}}

            <div class="form-group {{$errors->first('project_id', 'has-error')}}">
                <label for="projectId" class="control-label">Select Project</label>
                {{Form::select('project_id', $projects->toArray(), null, ['class' => 'form-control', 'placeholder' => 'Select a project', 'id' => 'projectId'])}}
                {!! $errors->first('project_id', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Copy</button>
            </div>

            {{Form::close()}}
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        $(function() {
            $('#copyWbsForm').on('submit', function() {
                $(this).find('button').prop('disabled', true).find('i.fa').toggleClass('fa-check fa-spinner fa-spin');
            });
        });
    </script>


@endsection