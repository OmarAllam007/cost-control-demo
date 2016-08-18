@extends('layouts.app')

@section('header')
    <h2>Edit Std activity resource</h2>

    <form action="{{ route('std-activity-resource.destroy', $std_activity_resource)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('std-activity-resource.show', $std_activity_resource)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('breakdown-template.show', $std_activity_resource->template)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($std_activity_resource, ['route' => ['std-activity-resource.update', $std_activity_resource]]) }}

        {{ method_field('patch') }}

        @include('std-activity-resource._form')

    {{ Form::close() }}
@stop
