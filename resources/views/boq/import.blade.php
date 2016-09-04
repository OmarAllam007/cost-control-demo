@extends('layouts.app')

@section('header')
    <h2 class="panel-title">Import BOQ -  {{$project->name}} </h2>

    <a href="{{route('boq.index')}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-right"></i> Back</a>
@endsection

@section('body')

    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['boq.post-import',$project], 'files' => true])}}
            {{csrf_field()}}
            <div class="form-group {{$errors->first('file', 'has-error')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">

                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Upload</button>
            </div>

            {{Form::close()}}
        </div>
    </div>

@endsection