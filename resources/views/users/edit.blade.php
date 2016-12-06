@extends('layouts.app')

@section('header')
    <h2>Edit User</h2>

    <form action="{{ route('users.destroy', $user)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('users.show', $user)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('users.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($user, ['route' => ['users.update', $user]]) }}

    {{ method_field('PATCH') }}

    @include('users._form')

    {{ Form::close() }}
@stop
