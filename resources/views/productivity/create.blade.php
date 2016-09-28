@extends('layouts.app')

@section('header')
    <h2>Add Productivity</h2>

    <a href="{{ route('productivity.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'productivity.store']) }}

        @include('productivity._form', ['override' => false])

    {{ Form::close() }}
@stop
