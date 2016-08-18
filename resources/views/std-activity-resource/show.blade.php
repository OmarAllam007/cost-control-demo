@extends('layouts.app')

@section('header')
<h2>Std activity resource</h2>

<form action="{{ route('std-activity-resource.destroy', $std_activity_resource)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('std-activity-resource.edit', $std_activity_resource)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('breakdown-template.show', $std_activity_resource->template)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')

@stop
