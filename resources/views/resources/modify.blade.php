@extends('layouts.app')

@section('header')
    <h2>Modify resources</h2>
    <a href="{{route('resources.index')}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['all-resources.post-modify','project'=>request('project')], 'files' => true])}}
            <div class="form-group {{$errors->first('file', 'has-errors')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>
@endsection