@extends('layouts.app')

@section('header')
    <h2>Edit Boq division</h2>

    <form action="{{ route('boq-division.destroy', $boq_division)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('boq-division.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($boq_division, ['route' => ['boq-division.update', $boq_division]]) }}

        {{ method_field('patch') }}

        @include('boq-division._form')

    {{ Form::close() }}
@stop
