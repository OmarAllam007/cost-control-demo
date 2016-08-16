@extends('layouts.app')

@section('header')
<h2>WBS Level</h2>

<form action="{{ route('wbs-level.destroy', $wbs_level)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('wbs-level.edit', $wbs_level)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('wbs-level.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($wbs_level, ['route' => ['wbs-level.update', $wbs_level]]) }}

{{ method_field('patch') }}

@include('wbs-level._form')

{{ Form::close() }}
@stop
