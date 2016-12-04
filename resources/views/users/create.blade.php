@extends('layouts.app')

@section('header')
    <h2>Add User</h2>

    <a href="{{ route('users.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => 'users.store']) }}

    @include('users._form', ['user' => new \App\User()])

    {{ Form::close() }}
@stop
