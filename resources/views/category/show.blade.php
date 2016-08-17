@extends('layouts.app')

@section('header')
<h2>Category</h2>

<form action="{{ route('category.destroy', $category)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('category.edit', $category)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('category.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($category, ['route' => ['category.update', $category]]) }}

{{ method_field('patch') }}

@include('category._form')

{{ Form::close() }}
@stop
