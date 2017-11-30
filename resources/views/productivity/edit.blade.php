@extends('layouts.app')

@section('header')
    <h2>
        @if ($productivity->project)
            {{$project->name}} &mdash;
        @endif

        Modify Productivity
    </h2>
@stop

@section('body')
    {{ Form::model($productivity, ['route' => ['productivity.update', $productivity]]) }}

        {{ method_field('patch') }}

        @include('productivity._form', ['override' => !empty($productivity->project_id)])

    {{ Form::close() }}
@stop
