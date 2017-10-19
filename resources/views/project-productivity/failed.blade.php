@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Modify Productivity</h2>
        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back to Project</a>
    </div>
@stop

@section('body')
    <p>Failed to import some records please click below to download failed records</p>

    <p>
        <a href="{{url($result['failed'])}}" class="btn btn-primary"><i class="fa fa-cloud-download"></i> Download</a>
    </p>
@endsection