@extends('layouts.app')

@section('header')
<h2>Csi category</h2>

<form action="{{ route('csi-category.destroy', $csi_category)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('csi-category.edit', $csi_category)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('csi-category.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($csi_category, ['route' => ['csi-category.update', $csi_category]]) }}

{{ method_field('patch') }}

@include('csi-category._form')

{{ Form::close() }}
@stop
