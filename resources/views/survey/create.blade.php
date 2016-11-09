@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Add Quantity Survey</h2>

    <a href="{{ route('project.show', request('project')) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'survey.store']) }}

    @include('survey._form')

    {{ Form::close() }}
@stop
