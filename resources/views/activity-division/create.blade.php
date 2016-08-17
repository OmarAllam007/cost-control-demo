@extends('layouts.app')

@section('header')
    <h2>Add Divisions</h2>

    <a href="{{ route('activity-division.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'activity-division.store']) }}

        @include('activity-division._form')

    {{ Form::close() }}
@stop
