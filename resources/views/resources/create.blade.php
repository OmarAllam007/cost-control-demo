@extends('layouts.app')

@section('header')
    <h2>Add Resources</h2>

    <a href="{{ route('resources.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'resources.store']) }}

        @include('resources._form')

    {{ Form::close() }}
@stop
