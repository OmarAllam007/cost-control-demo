@extends('layouts.app')

@section('title', 'Breakdown template failed import')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            @if ($project)
                {{ $project->name }} &mdash;
            @endif

            Breakdown Templates &mdash; Failed records
        </h2>

        <div class="btn-toolbar">
            @if ($project)
                <a class="btn btn-sm btn-default" href="{{ route('project.budget', $project) }}"><i class="fa fa-chevron-left"></i> Back to project</a>
            @else
                <a class="btn btn-sm btn-default" href="{{ route('breakdown-template.index') }}"><i class="fa fa-chevron-left"></i> Back to templates</a>
            @endif

            <a class="btn btn-sm btn-default" href="{{ route('breakdown-template.modify') . ($project? "?project={$project->id}" : '') }}"><i class="fa fa-chevron-left"></i> Back to import</a>
        </div>
    </div>
@endsection

@section('body')
    <main class="row">
        <article class="col-sm-9 col-md-6">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                {{ $success }} records have been imported. {{ $failed->count() }} records failed.
            </div>

            <p class="lead">Please click below to download failed records:</p>

            <div class="form-group">
                <a href="{{ url("/storage/$failed_file") }}" class="btn btn-primary"><i class="fa fa-download"></i> Download</a>
            </div>
        </article>
    </main>
@endsection