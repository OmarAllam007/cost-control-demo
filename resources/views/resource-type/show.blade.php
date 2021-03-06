@extends('layouts.app')

@section('header')
<h2>Resource type</h2>

<form action="{{ route('resource-type.destroy', $resource_type)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('resource-type.edit', $resource_type)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('resource-type.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($resource_type, ['route' => ['resource-type.update', $resource_type]]) }}

{{ method_field('patch') }}

@include('resource-type._form')

{{ Form::close() }}
@stop
