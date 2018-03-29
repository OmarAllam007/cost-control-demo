@extends('layouts.app')

@section('title', 'Communication Plan')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Create Role</h2>

        <a href="{{route('roles.index')}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')

    {{Form::model($role, ['url' => route('roles.update', $role)])}}

    {{method_field('put')}}

    @include('roles.form',compact('role'))

    {{Form::close()}}

@endsection