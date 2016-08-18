@extends('layouts.app')

@section('header')
    <h2>Add Std activity resource</h2>

    <a href="{{ route('std-activity-resource.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'std-activity-resource.store']) }}

        @include('std-activity-resource._form')

    {{ Form::close() }}
@stop
