@extends('layouts.app')

@section('title')
    {{ $project->name }} &mdash; Modify Breakdown
@endsection

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{ $project->name }} &mdash; Modify Breakdown
        </h2>

        <div class="btn-toolbar">
            <a href="{{ route('project.breakdown.import', $project) }}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back to import
            </a>

            <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back to project
            </a>
        </div>
    </div>
@endsection

@section('body')
    <p class="lead">Failed to import some records. Please click below to download failed records</p>

    <a href="{{url("/storage/{$status['failed']}")}}" class="btn btn-primary"><i class="fa fa-download"></i> Download</a>
@endsection