@extends('layouts.app')

@section('header')
    <h2>Override Productivity - {{$project->name}}</h2>

    <a href="{{ route('project.show', $project) }}#productivity" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::model($productivity, ['route' => ['productivity.post-override', $baseProductivity, $project]]) }}

    @include('productivity._form', ['override' => true, 'base' => $baseProductivity])

    {{ Form::close() }}
@stop
