@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Import Actual Revenue</h2>
    <a href="/project/cost_overdraft/{{$project->id}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">
            {{Form::open(['files' => true])}}
            <p class="text-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/actual_revenue.xlsx')}}">click here</a> to download a sample template
            </p>

            <div class="form-group {{$errors->first('period_id', 'has-error')}}">
                {{Form::label('period_id', null, ['class' => 'control-label'])}}
                {{Form::select('period_id', $periods, null, ['class' => 'form-control', 'placeholder' => 'Select Period'])}}
                {!! $errors->first('period_id', '<div class="help-block">:message</div>') !!}
            </div>

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