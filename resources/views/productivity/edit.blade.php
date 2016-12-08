@extends('layouts.app')

@section('header')
    <h2>Edit Productivity</h2>

    <form action="{{ route('productivity.destroy', $productivity)}}" class="pull-right" method="post">
        @can('delete', 'productivity')
        {{csrf_field()}} {{method_field('delete')}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan

        <a href="{{ route('productivity.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($productivity, ['route' => ['productivity.update', $productivity]]) }}

        {{ method_field('patch') }}

        @include('productivity._form', ['override' => false])

    {{ Form::close() }}
@stop
