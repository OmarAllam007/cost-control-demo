@extends('layouts.app')

@section('header')
    <h2>Add Breakdown template</h2>

    <a href="{{ route('breakdown-template.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'breakdown-template.store']) }}

        @include('breakdown-template._form')

    {{ Form::close() }}
@stop
