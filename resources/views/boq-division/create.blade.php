@extends('layouts.app')

@section('header')
    <h2>Add Boq division</h2>

    <a href="{{ route('boq-division.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'boq-division.store']) }}

        @include('boq-division._form')

    {{ Form::close() }}
@stop
