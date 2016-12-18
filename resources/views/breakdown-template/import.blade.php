@extends('layouts.app')

@section('header')
    <h2>Import Breakdown Template</h2>
    <a href="{{route('breakdown-template.index')}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => 'breakdown-template.post-import', 'files' => true])}}
            {{csrf_field()}}
            <p class="text-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/breakdown-templates.xlsx')}}">click here</a> to download a sample template
            </p>

            <div class="form-group {{$errors->first('file', 'has-error')}}">
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