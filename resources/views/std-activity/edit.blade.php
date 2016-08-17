@extends('layouts.app')

@section('header')
    <h2>Edit Std activity</h2>

    <form action="{{ route('std-activity.destroy', $std_activity)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('std-activity.show', $std_activity)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('std-activity.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($std_activity, ['route' => ['std-activity.update', $std_activity]]) }}

        {{ method_field('patch') }}

        @include('std-activity._form')

    {{ Form::close() }}
@stop
