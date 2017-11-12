@extends('layouts.app')

@section('title', 'Cost Man Days')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Cost Man Days &mdash; {{$project->name}}</h2>

        <div class="text-right">
            <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back to Project
            </a>
        </div>
    </div>
@endsection

@section('body')
    <div class="alert alert-success">
        {{$result['success']}} Items have been imported
    </div>

    <p class="lead">Some records could not be imported. Please click below to download failed records.</p>

    <div class="form-group">
        <a href="{{$result['failed']}}" class="btn btn-primary"><i class="fa fa-download"></i> Download</a>
    </div>
@endsection