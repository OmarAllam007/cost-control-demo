@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Add Boq</h2>

    <a href="{{ route('project.show', request('project')) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['route' => ['boq.store', 'project' => request('project')]]) }}

        @include('boq._form')

    {{ Form::close() }}
@stop
