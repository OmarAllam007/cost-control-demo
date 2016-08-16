@extends('layouts.app')

@section('header')
<h2>Survey</h2>

<form action="{{ route('survey.destroy', $survey)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('survey.edit',$survey)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('survey.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
{{ Form::model($survey, ['route' => ['survey.update', $survey]]) }}

{{ method_field('patch') }}

@include('survey._form')

{{ Form::close() }}
@stop
