@extends('home.master-data')

@section('header')
    <h2>Modify resources</h2>
    <a href="{{route('resources.index')}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['all-resources.post-modify','project'=>request('project')], 'files' => true])}}

            <div class="alert alert-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/modify-resources.xlsx')}}">click here</a> to download a sample template
            </div>

            <div class="form-group {{$errors->first('file', 'has-errors')}}">
                {{Form::label('file', null, ['class' => 'control-label sr-only'])}}
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