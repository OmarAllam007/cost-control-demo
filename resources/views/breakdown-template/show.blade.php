@extends('layouts.app')

@section('header')
<h2>Breakdown template</h2>

<form action="{{ route('breakdown-template.destroy', $breakdown_template)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('breakdown-template.edit', $breakdown_template)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('breakdown-template.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')

@stop
