@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Import Resources</h2>

        @if ($status['project'])
            <a class="btn btn-sm btn-default" href="{{route('project.budget', $status['project'])}}"><i class="fa fa-chevron-left"></i> Back to Project</a>
        @else
            <div class="btn-toolbar">
                <a class="btn btn-sm btn-primary" href="{{route('resources.import')}}"><i class="fa fa-upload"></i> Back to import</a>
                <a class="btn btn-sm btn-default" href="{{route('project.index')}}"><i class="fa fa-chevron-left"></i> Back to Resources</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    <div class="alert alert-info">
        {{$status['success']}} resources have been imported successfully.
    </div>

    <div class="form-group">
        <a href="{{$status['result_file']}}" class="btn btn-primary"><i class="fa fa-download"></i> Download Import Result</a>
    </div>
@endsection