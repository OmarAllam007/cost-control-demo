@extends('layouts.app')

@section('header')
    <h2>Add Resource type</h2>

    <a href="{{ route('resource-type.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'resource-type.store']) }}

        @include('resource-type._form')

    {{ Form::close() }}
@stop
