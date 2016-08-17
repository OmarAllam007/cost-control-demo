@extends('layouts.app')

@section('header')
    <h2>Edit Division</h2>

    <form action="{{ route('activity-division.destroy', $activity_division)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('activity-division.show', $activity_division)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('activity-division.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($activity_division, ['route' => ['activity-division.update', $activity_division]]) }}

        {{ method_field('patch') }}

        @include('activity-division._form')

    {{ Form::close() }}
@stop
