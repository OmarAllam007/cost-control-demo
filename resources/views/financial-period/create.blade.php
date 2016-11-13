@extends('layouts.app')
@section('header')
    <h2>Add Financial Period - {{$project->name}}</h2>
    <a href="{{ route('project.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    <form action="{{route('financial.store',$project->id)}}" method="post">
        @include('financial-period._form')
    </form>

@stop
