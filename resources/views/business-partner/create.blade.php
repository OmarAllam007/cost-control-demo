@extends('layouts.app')

@section('header')
    <h2>Add Business partner</h2>

    <a href="{{ route('business-partner.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'business-partner.store']) }}

        @include('business-partner._form')

    {{ Form::close() }}
@stop
