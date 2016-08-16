@extends('layouts.app')

@section('header')
<h2>Resources</h2>

<form action="{{ route('resources.destroy', $resource)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('resources.edit', $resource)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('resources.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($resource, ['route' => ['resources.update', $resource]]) }}

{{ method_field('patch') }}

@include('resources._form')

{{ Form::close() }}
@stop
