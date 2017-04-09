@extends('layouts.app')

@section('header')
    <h2>Edit fiscal period</h2>
    <a href="{{ route('project.cost-control', $period->project) }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop

@section('body')
    {{ Form::model($period, ['route' => ['period.update', $period], 'method' => 'patch']) }}

    @include('period._form')

    {{ Form::close() }}
@stop