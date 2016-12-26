@extends('layouts.app')

@section('header')
<h2>{{$project->name}} &mdash; Create Fiscal Period</h2>

<a href="{{ route('project.cost-control', $project) }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::open(['url' => route('period.store', ['project' => $project])]) }}

    @include('period._form')

    {{ Form::close() }}
@stop