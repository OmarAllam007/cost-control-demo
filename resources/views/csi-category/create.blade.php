@extends('layouts.app')

@section('header')
    <h2>Add Csi category</h2>

    <a href="{{ route('csi-category.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'csi-category.store']) }}

        @include('csi-category._form')

    {{ Form::close() }}
@stop
