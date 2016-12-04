@extends('layouts.app')

@section('header')
    <h2>Modify Productivities</h2>
    <a href="{{route('resources.index')}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => 'all-productivities.post-modify', 'files' => true])}}

            <p class="text-info">
                <i class="fa fa-download"></i> Please download Excel Files
            </p>

            <div class="form-group {{$errors->first('file', 'has-errors')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Modify
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>
@endsection