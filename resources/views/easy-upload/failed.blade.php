@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Easy Upload</h2>
@endsection

@section('body')
    <div class="alert alert-info">
        <p>{{$status['success']}} Records has been imported successfully. But failed to upload some records.</p>

    </div>

    <p class="lead">Please click below to download failed records</p>

    <div class="mt-20">
    <a href="{{$status['failed']}}" class="btn btn-primary">
        <i class="fa fa-download"></i> Download
    </a>
    </div>
@endsection