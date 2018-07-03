@extends('layouts.app')

@section('title', 'Update Progress')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Update Progress</h2>
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default">
            <i class="fa fa-chevron-left"></i> Back to Project
        </a>
    </div>
@endsection

@section('body')
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i>
        {{$result['success']}} Records have been imported.
    </div>

    <p class="lead">
        Failed to import some records. Please click below to download failed records.
    </p>

    <a href="{{url($result['failed'])}}" class="btn btn-primary btn-lg">
        <i class="fa fa-download"></i> Download
    </a>
@endsection