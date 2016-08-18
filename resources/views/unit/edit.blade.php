@extends('layouts.app')

@section('header')
    <h2>Edit Unit</h2>

    <form action="{{ route('unit.destroy', $unit)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('unit.show', $unit)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('unit.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($unit, ['route' => ['unit.update', $unit]]) }}

        {{ method_field('PATCH') }}

        @include('unit._form')

    {{ Form::close() }}
@stop
