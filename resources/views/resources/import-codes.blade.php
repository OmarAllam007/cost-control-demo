@extends('layouts.app')

@section('header')
    <h2>
        Import equivalent resource codes
        @if ($project_id = request('project'))
        &mdash; {{App\Project::find($project_id)->name}}
        @endif
    </h2>

    @if ($project_id)
        <a href="{{route('project.cost-control', $project_id)}}" class="btn btn-default btn-sm pull-right">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    @else
        <a href="{{route('resources.index')}}" class="btn btn-default btn-sm pull-right">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    @endif
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['resources.post-import-codes', 'project' => $project_id], 'files' => true])}}

            <p class="text-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/resource-map.xlsx')}}">click
                    here</a> to download a sample template
            </p>

            <div class="form-group {{$errors->first('file', 'has-errors')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>
@endsection