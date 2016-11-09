@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Edit Survey</h2>

    <form action="{{ route('survey.destroy', $survey)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('project.show', $survey->project_id)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($survey, ['route' => ['survey.update', $survey]]) }}

    {{ method_field('patch') }}

    @include('survey._form')

    {{ Form::close() }}
@stop
