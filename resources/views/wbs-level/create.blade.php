@extends('layouts.' . (request('iframe') ? 'iframe' : 'app'))

@section('header')
    <h2>Add level</h2>

    <a href="{{ route('project.show', request('project')) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'wbs-level.store']) }}

        @include('wbs-level._form')

    {{ Form::close() }}
@stop
