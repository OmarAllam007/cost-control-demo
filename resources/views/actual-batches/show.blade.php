@extends('layouts.iframe')

@section('body')

    <div class="row">

        <div class="col-sm-4">
            <strong>Uploaded By: </strong>{{$batch->user->name}}
        </div>
        <div class="col-sm-4">
            <strong>Uploaded at: </strong>{{$batch->created_at->format('d/m/Y H:i')}}
        </div>
        <div class="col-sm-4">
            <strong><i class="fa fa-download"></i> <a href="{{$batch->file}}">Download</a></strong>
        </div>
    </div>

    <h3 class="page-header">Issues</h3>

    @forelse($batch->issues as $issue)
        @include('actual-batches.issues.' . $issue->type, compact('issue'))
    @empty
        <div class="alert alert-info">No issues found on this upload</div>
    @endforelse

@stop