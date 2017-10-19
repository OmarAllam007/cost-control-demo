@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Modify Productivity</h2>
@stop

@section('body')
    {{ Form::model($productivity, ['route' => ['productivity.update', $productivity]]) }}

        {{ method_field('patch') }}

        @include('productivity._form', ['override' => false])

    {{ Form::close() }}
@stop
