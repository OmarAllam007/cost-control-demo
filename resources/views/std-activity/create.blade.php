@extends('layouts.app')

@section('header')
    <h2>Add Standard activity</h2>

    <a href="{{ route('std-activity.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'std-activity.store']) }}

        @include('std-activity._form')

    {{ Form::close() }}
@stop
