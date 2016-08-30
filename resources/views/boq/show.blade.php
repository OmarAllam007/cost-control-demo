@extends('layouts.app')

@section('header')
<h2>Boq</h2>

<form action="{{ route('boq.destroy', $boq)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('boq.edit', $boq)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('boq.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($boq, ['route' => ['boq.update', $boq]]) }}

{{ method_field('patch') }}

@include('boq._form')

{{ Form::close() }}
@stop
