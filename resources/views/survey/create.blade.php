@extends('layouts.app')

@section('header')
    <h2>Add Survey</h2>

    <a href="{{ route('survey.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'survey.store']) }}
        @include('survey._form')


    {{ Form::close() }}
@stop
