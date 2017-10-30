@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <div class="display-flex">
        <h4 class="flex">Import BOQ</h4>
        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to project
        </a>
    </div>
@endsection

@section('body')
    <p class="lead">Failed to import some records please click below to download failed records</p>

    <p>
        <a href="{{url($status['failed'])}}" class="btn btn-primary"><i class="fa fa-cloud-download"></i> Download</a>
    </p>
@endsection