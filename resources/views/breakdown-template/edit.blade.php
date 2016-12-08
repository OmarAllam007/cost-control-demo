@extends('layouts.app')

@section('header')
    <h2>Edit template</h2>

    <form action="{{ route('breakdown-template.destroy', $breakdown_template)}}" class="pull-right" method="post">
        <a href="{{ route('breakdown-template.show', $breakdown_template)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        @can('delete', 'breakdown-template')
        {{csrf_field()}} {{method_field('delete')}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan
        <a href="{{ route('breakdown-template.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($breakdown_template, ['route' => ['breakdown-template.update', $breakdown_template]]) }}

        {{ method_field('patch') }}

        @include('breakdown-template._form')

    {{ Form::close() }}
@stop
