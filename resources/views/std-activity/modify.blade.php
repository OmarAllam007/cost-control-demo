@extends('home.master-data')

@section('header')
    <h2>Modify Standard Activities</h2>
    <a href="{{route('resources.index')}}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-chevron-left"></i> Back
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['all-stdActivites.post-modify'], 'files' => true])}}
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