@extends('layouts.app')

@section('header')
    <h2>Add Unit</h2>

    <a href="{{ route('unit.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'unit.store']) }}

        @include('unit._form')

    {{ Form::close() }}
@stop
