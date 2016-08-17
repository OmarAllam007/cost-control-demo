@extends('layouts.app')

@section('header')
    <h2>Add Category</h2>

    <a href="{{ route('category.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'category.store']) }}

        @include('category._form')

    {{ Form::close() }}
@stop
