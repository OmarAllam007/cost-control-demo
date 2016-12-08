@extends('layouts.app')

@section('header')
    <h2>Add Project</h2>

    <a href="{{ route('project.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'project.store']) }}
    @include('project._form', ['project' => new App\Project]);
    {{ Form::close() }}
@stop
