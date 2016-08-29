@extends('layouts.app')

@section('header')
    <h2>Add Boq</h2>

    <a href="{{ route('boq.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'boq.store']) }}

        @include('boq._form')

    {{ Form::close() }}
@stop
