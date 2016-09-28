@extends('layouts.app')

@section('header')
    <h2>Override Resource - {{$project->name}}</h2>

    <a href="{{ route('project.show', $project) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::model($resource, ['route' => ['resources.post-override', $baseResource, $project]]) }}

        @include('resources._form', ['override' => true, 'base' => $baseResource])

    {{ Form::close() }}
@stop
